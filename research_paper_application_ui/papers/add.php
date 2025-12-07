<?php
require_once "../db.php";
require_once "../header.php";

// Load departments and categories for dropdowns
$departments = $pdo->query("SELECT dept_id, dept_name FROM department ORDER BY dept_name")->fetchAll();
$categories  = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_name")->fetchAll();

$errors = [];
$successMessage = "";

// Helper for repopulating fields
function old($key) {
    return isset($_POST[$key]) ? htmlspecialchars($_POST[$key]) : "";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // -----------------------------
    // Collect inputs
    // -----------------------------
    $title          = trim($_POST["title"] ?? "");
    $abstract       = trim($_POST["abstract"] ?? "");
    $published_year = (int)($_POST["published_year"] ?? 0);
    $dept_id        = (int)($_POST["dept_id"] ?? 0);
    $category_id    = (int)($_POST["category_id"] ?? 0);
    $authors_raw    = trim($_POST["authors"] ?? "");
    $keywords_raw   = trim($_POST["keywords"] ?? "");
    $author_role    = $_POST["author_role"] ?? ""; 
    $upload_date    = date("Y-m-d");

    // Convert authors list
    $authorNames = array_filter(array_map("trim", explode(",", $authors_raw)));

    // Convert keywords list
    $keywordTerms = [];
    if ($keywords_raw !== "") {
        $keywordTerms = array_unique(array_filter(array_map("trim", explode(",", $keywords_raw))));
    }

    // -----------------------------
    // Validation
    // -----------------------------
    if ($title === "")      $errors[] = "Title is required.";
    if ($abstract === "")   $errors[] = "Abstract is required.";
    if ($published_year < 1900 || $published_year > 2100)
        $errors[] = "Published year must be between 1900 and 2100.";
    if ($dept_id <= 0)      $errors[] = "Department is required.";
    if ($category_id <= 0)  $errors[] = "Category is required.";
    if (count($authorNames) === 0)
        $errors[] = "At least one author is required.";
    if (!in_array($author_role, ["Student", "Faculty"]))
        $errors[] = "Author role must be Student or Faculty.";

    // PDF validation
    $pdf_link = null;
    if (!empty($_FILES["pdf_file"]["name"])) {

        if ($_FILES["pdf_file"]["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading PDF file.";
        } else {
            $mime = mime_content_type($_FILES["pdf_file"]["tmp_name"]);
            if ($mime !== "application/pdf")
                $errors[] = "Only PDF files are allowed.";
        }
    }

    // -----------------------------
    // Insert logic
    // -----------------------------
    if (empty($errors)) {

        try {
            $pdo->beginTransaction();

            // Save PDF
            if (!empty($_FILES["pdf_file"]["name"])) {

                $uploadDir = __DIR__ . "/../uploads";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $safeName = preg_replace("/[^A-Za-z0-9_\.-]/", "_", basename($_FILES["pdf_file"]["name"]));
                $fileName = time() . "_" . $safeName;
                $targetFs = $uploadDir . "/" . $fileName;

                if (!move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $targetFs)) {
                    throw new RuntimeException("Failed to move uploaded file.");
                }

                $pdf_link = "/research_app/uploads/" . $fileName;
            }

            // Insert paper
            $paperStmt = $pdo->prepare("
                INSERT INTO paper (title, abstract, published_year, dept_id, category_id, upload_date, pdf_link)
                VALUES (:title, :abstract, :year, :dept, :cat, :upload, :pdf)
            ");

            $paperStmt->execute([
                ":title"  => $title,
                ":abstract" => $abstract,
                ":year"   => $published_year,
                ":dept"   => $dept_id,
                ":cat"    => $category_id,
                ":upload" => $upload_date,
                ":pdf"    => $pdf_link
            ]);

            $paper_id = $pdo->lastInsertId();

            // Insert authors → member + paperauthor
            foreach ($authorNames as $authorName) {

                $checkStmt = $pdo->prepare("
                    SELECT member_id FROM member 
                    WHERE name = :name AND dept_id = :dept_id
                    LIMIT 1
                ");
                $checkStmt->execute([
                    ":name" => $authorName,
                    ":dept_id" => $dept_id
                ]);

                $member_id = $checkStmt->fetchColumn();

                if (!$member_id) {
                    $insertMember = $pdo->prepare("
                        INSERT INTO member (name, email, role, dept_id)
                        VALUES (:name, NULL, :role, :dept)
                    ");
                    $insertMember->execute([
                        ":name" => $authorName,
                        ":role" => $author_role,
                        ":dept" => $dept_id
                    ]);

                    $member_id = $pdo->lastInsertId();
                }

                $linkStmt = $pdo->prepare("
                    INSERT INTO paperauthor (paper_id, member_id)
                    VALUES (:paper, :member)
                ");
                $linkStmt->execute([
                    ":paper" => $paper_id,
                    ":member" => $member_id
                ]);
            }

            // Insert keywords
            foreach ($keywordTerms as $term) {

                $checkKW = $pdo->prepare("
                    SELECT keyword_id FROM keyword WHERE keyword = :kw LIMIT 1
                ");
                $checkKW->execute([":kw" => $term]);
                $keyword_id = $checkKW->fetchColumn();

                if (!$keyword_id) {
                    $insertKW = $pdo->prepare("
                        INSERT INTO keyword (keyword) VALUES (:kw)
                    ");
                    $insertKW->execute([":kw" => $term]);
                    $keyword_id = $pdo->lastInsertId();
                }

                $linkKW = $pdo->prepare("
                    INSERT INTO paperkeyword (paper_id, keyword_id)
                    VALUES (:paper, :kw)
                ");
                $linkKW->execute([
                    ":paper" => $paper_id,
                    ":kw" => $keyword_id
                ]);
            }

            $pdo->commit();

            $successMessage = "Paper successfully added! ID = " . $paper_id;
            $_POST = []; 

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "ERROR: " . $e->getMessage();
        }
    }
}
?>

<div class="card p-4 shadow mb-5">
    <h3 class="mb-3">Add New Research Paper</h3>
    <hr>

   
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h6 class="mb-2">Please fix the following issue(s):</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" required value="<?= old('title') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Abstract *</label>
            <textarea name="abstract" class="form-control" rows="4" required><?= old('abstract') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Published Year *</label>
            <input type="number" name="published_year" min="1900" max="2100"
                   class="form-control" required value="<?= old('published_year') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Department *</label>
            <select name="dept_id" class="form-select" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['dept_id'] ?>"
                        <?= old('dept_id') == $d['dept_id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($d['dept_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Category *</label>
            <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>"
                        <?= old('category_id') == $c['category_id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Authors (comma-separated) *</label>
            <input type="text" name="authors" class="form-control"
                   placeholder="e.g. Alan Turing, Ada Lovelace"
                   required value="<?= old('authors') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Author Role *</label>
            <select name="author_role" class="form-select" required>
                <option value="">Select Role</option>
                <option value="Student" <?= old("author_role")=="Student"?"selected":"" ?>>Student</option>
                <option value="Faculty" <?= old("author_role")=="Faculty"?"selected":"" ?>>Faculty</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Keywords (comma-separated)</label>
            <input type="text" name="keywords" class="form-control"
                   placeholder="AI, Database Systems, Security"
                   value="<?= old('keywords') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload PDF (optional)</label>
            <input type="file" name="pdf_file" class="form-control">
        </div>

        <button class="btn btn-success">Add Paper</button>
    </form>
</div>

<?php require_once "../footer.php"; ?>
