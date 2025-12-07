<?php
include "../db.php";
include "../header.php";

$query = "";
$papers = [];

/* ---------------------------------------------------------
   Get Authors from paperauthor + member
----------------------------------------------------------*/
function getAuthors($pdo, $paper_id) {
    $sql = "
        SELECT m.name
        FROM member m
        JOIN paperauthor pa ON m.member_id = pa.member_id
        WHERE pa.paper_id = :pid
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pid' => $paper_id]);
    $authors = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return implode(", ", $authors);
}

/* ---------------------------------------------------------
   Search Logic
----------------------------------------------------------*/
if (!empty($_GET['q'])) {

    $query = trim($_GET['q']);
    $like = "%$query%";

    $sql = "
        SELECT DISTINCT
            p.paper_id,
            p.title,
            p.abstract,
            p.published_year,
            p.upload_date,
            p.pdf_link,
            d.dept_name
        FROM paper p
        LEFT JOIN department d ON p.dept_id = d.dept_id
        LEFT JOIN paperauthor pa ON pa.paper_id = p.paper_id
        LEFT JOIN member m ON m.member_id = pa.member_id
        WHERE 
               p.title LIKE :title
            OR p.abstract LIKE :abstract
            OR d.dept_name LIKE :dept
            OR CAST(p.published_year AS CHAR) LIKE :year
            OR m.name LIKE :author
        ORDER BY p.paper_id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":title",   $like, PDO::PARAM_STR);
    $stmt->bindValue(":abstract",$like, PDO::PARAM_STR);
    $stmt->bindValue(":dept",    $like, PDO::PARAM_STR);
    $stmt->bindValue(":year",    $like, PDO::PARAM_STR);
    $stmt->bindValue(":author",  $like, PDO::PARAM_STR);

    $stmt->execute();
    $papers = $stmt->fetchAll();
}

?>

<h2>Search Papers</h2>

<form method="GET" class="mb-3">
    <input 
        type="text" 
        name="q" 
        value="<?= htmlspecialchars($query) ?>" 
        class="form-control" 
        placeholder="Enter title, author, abstract keyword, department, or year"
        autofocus
    />
</form>

<?php if (!empty($query)): ?>
<p>
    <strong><?= count($papers) ?></strong> result(s) found for 
    <em>"<?= htmlspecialchars($query) ?>"</em>
</p>
<?php endif; ?>

<?php if (!empty($papers)): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Authors</th>
            <th>Year</th>
            <th>Uploaded</th>
            <th>Department</th>
            <th>PDF</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($papers as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['paper_id']) ?></td>

            <td>
                <a href="paper_view.php?id=<?= $row['paper_id'] ?>">
                    <?= htmlspecialchars($row['title']) ?>
                </a>
            </td>

            <td><?= htmlspecialchars(getAuthors($pdo, $row['paper_id'])) ?></td>

            <td><?= htmlspecialchars($row['published_year']) ?></td>
            <td><?= htmlspecialchars($row['upload_date']) ?></td>
            <td><?= htmlspecialchars($row['dept_name']) ?></td>

            <td>
                <?php if (!empty($row['pdf_link'])): ?>
                    <a href="<?= htmlspecialchars($row['pdf_link']) ?>" 
                       class="btn btn-sm btn-primary" target="_blank">
                       PDF
                    </a>
                <?php else: ?>
                    <span class="text-muted">No PDF</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php elseif (!empty($query)): ?>
    <p>No papers found.</p>
<?php endif; ?>

<?php include "../footer.php"; ?>
