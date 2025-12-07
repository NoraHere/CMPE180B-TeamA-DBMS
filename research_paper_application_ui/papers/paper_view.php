<?php
include "../db.php";
include "../header.php";

$paper_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$paper_id) {
    die("Invalid paper ID");
}

/* ---------------------------------------------------------
   Handle new review POST
----------------------------------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {

    $faculty_id = (int)$_POST['faculty_id'];
    $score      = (int)$_POST['score'];
    $feedback   = trim($_POST['feedback']);

    if ($faculty_id && $score && $feedback !== '') {
        $sql = "
            INSERT INTO review (paper_id, faculty_id, score, feedback, review_date)
            VALUES (:pid, :fid, :score, :feedback, CURDATE())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'pid'      => $paper_id,
            'fid'      => $faculty_id,
            'score'    => $score,
            'feedback' => $feedback
        ]);
    }

    header("Location: paper_view.php?id=" . $paper_id);
    exit;
}

/* ---------------------------------------------------------
   Handle new comment POST
----------------------------------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {

    $member_id    = (int)$_POST['member_id'];
    $comment_text = trim($_POST['comment_text']);

    if ($member_id && $comment_text !== '') {
        $sql = "
            INSERT INTO comment (paper_id, member_id, comment_text, timestamp)
            VALUES (:pid, :mid, :text, NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'pid'  => $paper_id,
            'mid'  => $member_id,
            'text' => $comment_text
        ]);
    }

    header("Location: paper_view.php?id=" . $paper_id);
    exit;
}


/* ---------------------------------------------------------
   Paper details
----------------------------------------------------------*/
$sql = "
    SELECT p.*, d.dept_name, c.category_name
    FROM paper p
    LEFT JOIN department d ON p.dept_id = d.dept_id
    LEFT JOIN category c ON p.category_id = c.category_id
    WHERE p.paper_id = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $paper_id]);
$paper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paper) {
    echo "<p>Paper not found.</p>";
    include "../footer.php";
    exit;
}


/* ---------------------------------------------------------
   Authors
----------------------------------------------------------*/
$sql_auth = "
    SELECT m.name
    FROM member m
    JOIN paperauthor pa ON m.member_id = pa.member_id
    WHERE pa.paper_id = :id
";
$stmt = $pdo->prepare($sql_auth);
$stmt->execute(['id' => $paper_id]);
$authors_rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
$authors = $authors_rows ? implode(", ", $authors_rows) : "Unknown";

/* ---------------------------------------------------------
   Reviews
----------------------------------------------------------*/
$sql_rev = "
    SELECT r.*, m.name AS reviewer
    FROM review r
    JOIN member m ON r.faculty_id = m.member_id
    WHERE r.paper_id = :id
    ORDER BY r.review_date DESC
";
$stmt = $pdo->prepare($sql_rev);
$stmt->execute(['id' => $paper_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   Comments
----------------------------------------------------------*/
$sql_com = "
    SELECT c.*, m.name AS commenter
    FROM comment c
    JOIN member m ON c.member_id = m.member_id
    WHERE c.paper_id = :id
    ORDER BY c.timestamp DESC
";
$stmt = $pdo->prepare($sql_com);
$stmt->execute(['id' => $paper_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   Member list for dropdowns
----------------------------------------------------------*/
$members = $pdo->query("SELECT member_id, name FROM member ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= htmlspecialchars($paper['title']) ?></h2>

<p><strong>Authors:</strong> <?= htmlspecialchars($authors) ?></p>
<p><strong>Department:</strong> <?= htmlspecialchars($paper['dept_name']) ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($paper['category_name']) ?></p>
<p><strong>Published:</strong> <?= htmlspecialchars($paper['published_year']) ?></p>

<?php if (!empty($paper['pdf_link'])) { ?>
    <p>
        <a href="<?= htmlspecialchars($paper['pdf_link']) ?>" class="btn btn-primary" target="_blank">
            View PDF
        </a>
    </p>
<?php } ?>

<hr>

<h3>Abstract</h3>
<p><?= nl2br(htmlspecialchars($paper['abstract'])) ?></p>

<hr>

<!-- ============================================
     ADD REVIEW FORM
============================================= -->
<h3>Add Review</h3>
<form method="POST" class="border p-3 mb-4">

    <input type="hidden" name="add_review" value="1">

    <div class="mb-2">
        <label><strong>Reviewer (Faculty)</strong></label>
        <select name="faculty_id" class="form-control" required>
            <option value="">-- Select Faculty --</option>
            <?php foreach ($members as $m) { ?>
                <option value="<?= $m['member_id'] ?>">
                    <?= htmlspecialchars($m['name']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-2">
        <label><strong>Score</strong></label>
        <select name="score" class="form-control" required>
            <option value="">-- Score --</option>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-2">
        <label><strong>Feedback</strong></label>
        <textarea name="feedback" rows="3" class="form-control" required></textarea>
    </div>

    <button class="btn btn-success">Submit Review</button>
</form>

<hr>

<h3>Reviews</h3>
<?php if (empty($reviews)) { ?>
    <p>No reviews yet.</p>
<?php } else { ?>
    <?php foreach ($reviews as $r) { ?>
        <div class="border p-2 mb-2">
            <strong><?= htmlspecialchars($r['reviewer']) ?></strong>
            <span class="text-muted">(Score: <?= htmlspecialchars($r['score']) ?>)</span>
            <p><?= nl2br(htmlspecialchars($r['feedback'])) ?></p>
            <small class="text-muted"><?= htmlspecialchars($r['review_date']) ?></small>
        </div>
    <?php } ?>
<?php } ?>

<hr>

<!-- ============================================
     ADD COMMENT FORM
============================================= -->
<h3>Add Comment</h3>
<form method="POST" class="border p-3 mb-4">

    <input type="hidden" name="add_comment" value="1">

    <div class="mb-2">
        <label><strong>Your Name</strong></label>
        <select name="member_id" class="form-control" required>
            <option value="">-- Select Member --</option>
            <?php foreach ($members as $m) { ?>
                <option value="<?= $m['member_id'] ?>">
                    <?= htmlspecialchars($m['name']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-2">
        <label><strong>Comment</strong></label>
        <textarea name="comment_text" rows="3" class="form-control" required></textarea>
    </div>

    <button class="btn btn-primary">Submit Comment</button>
</form>

<hr>

<h3>Comments</h3>
<?php if (empty($comments)) { ?>
    <p>No comments yet.</p>
<?php } else { ?>
    <?php foreach ($comments as $c) { ?>
        <div class="border p-2 mb-2">
            <strong><?= htmlspecialchars($c['commenter']) ?></strong>
            <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
            <small class="text-muted"><?= htmlspecialchars($c['timestamp']) ?></small>
        </div>
    <?php } ?>
<?php } ?>

<?php include "../footer.php"; ?>
