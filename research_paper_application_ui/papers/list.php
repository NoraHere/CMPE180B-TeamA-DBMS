<?php
include "../db.php";
include "../header.php";

/* ---------------------------------------------------------
   Pagination Setup
----------------------------------------------------------*/
$limit = 50;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ---------------------------------------------------------
   Function: Get Authors for a Paper
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
   Get Total Count
----------------------------------------------------------*/
$total = $pdo->query("SELECT COUNT(*) FROM paper")->fetchColumn();
$pages = ceil($total / $limit);

/* ---------------------------------------------------------
   Fetch Papers (with department)
----------------------------------------------------------*/
$sql = "
    SELECT 
        p.paper_id,
        p.title,
        p.published_year,
        p.upload_date,
        p.pdf_link,
        d.dept_name
    FROM paper p
    LEFT JOIN department d ON p.dept_id = d.dept_id
    ORDER BY p.paper_id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();

$papers = $stmt->fetchAll();
?>

<h2>All Papers</h2>

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
          <td><?= $row['paper_id'] ?></td>

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

<nav>
  <ul class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

<?php include "../footer.php"; ?>
