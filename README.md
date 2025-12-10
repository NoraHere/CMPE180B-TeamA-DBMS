# CMPE 180B - TeamA - DBMS
## University Research Repository (FINAL_URP)

This project implements a **University Research Repository (URP)** system for managing research papers, authors, departments, reviews, comments, and keywords. It demonstrates a complete DBMS workflow including:

- SQL schema creation  
- Data population using a large research paper dataset  
- CRUD functionality  
- Complex analytical queries  
- Indexing and performance improvements  
- ACID transactions and concurrency control  
- Automated tests  
- Backup & recovery  

---

## 1. Repository Structure

```
├── .venv/                          # Python virtual environment
├── backup/
│   ├── backup_final_urp.bat        # Automated backup script (ignored in git)
│   └── final_urp_YYYY-MM-DD_*.sql  # Timestamped backups (ignored in git)
├── data/
│   └── dblp-v10.csv                # Research dataset (ignored in git)
├── env/
├── research_paper_application_ui/  # UI application folder
├── sql/
│   ├── Category.sql
│   ├── Comment.sql
│   ├── Department.sql
│   ├── Keyword.sql
│   ├── Member.sql
│   ├── Paper.sql
│   ├── PaperAuthor.sql
│   ├── PaperKeyword.sql
│   └── Review.sql
├── src/
│   ├── concurrency solution.ipynb
│   ├── DBMS_Final_TeamA.ipynb
│   ├── testcases.ipynb
│   └── Transactions.ipynb
└── README.md
```

---

## 2. Environment Setup

### 2.1 Requirements
- **MySQL 8.x**
- **Python 3.10+**
- **pip**
- **Jupyter Notebook** (optional but recommended)

### 2.2 Download dataset

1. Visit the Kaggle dataset: [Research Papers Dataset](https://www.kaggle.com/datasets/nechbamohammed/research-papers-dataset)
2. Download the dataset (requires Kaggle account)
3. Extract `dblp-v10.csv` from the downloaded archive
4. Place `dblp-v10.csv` in the `data/` folder:
   ```
   data/
   └── dblp-v10.csv
   ```

**Note:** The `dblp-v10.csv` file is not included in the repository due to its large size.

### 2.3 Create virtual environment

```bash
python -m venv .venv

# Windows:
.venv\Scripts\activate

# Mac/Linux:
source .venv/bin/activate
```

### 2.4 Install dependencies

Create a `requirements.txt` if not included:

```
mysql-connector-python
jupyter
```

Then install:

```bash
pip install -r requirements.txt
```

---

## 3. Running the Project (Complete Workflow)

Follow these steps in order:

### Step 1: Setup environment

```bash
# Activate virtual environment
source .venv/bin/activate  # Mac/Linux
.venv\Scripts\activate     # Windows

# Launch Jupyter Notebook
jupyter notebook
```

### Step 2: Generate SQL files

Open and run **`src/DBMS_Final_TeamA.ipynb`**

**Note:** The SQL files are already generated and stored in the `sql/` folder. You only need to run this notebook if you want to regenerate the data with different random seeds or modify the data generation logic.

This notebook:
- Reads `data/dblp-v10.csv` (must be downloaded first from Kaggle)
- Automatically creates `sql/` directory if it doesn't exist
- Generates 9 `.sql` files in the `sql/` folder
- Processes 1,500 papers and 4,900+ members
- Creates all relationships and foreign keys

**Expected output:** 
```
Created 'sql/' directory

Generated: sql/Department.sql
Generated: sql/Member.sql
Generated: sql/Category.sql
Generated: sql/Paper.sql
Generated: sql/PaperAuthor.sql
Generated: sql/Keyword.sql
Generated: sql/PaperKeyword.sql
Generated: sql/Comment.sql
Generated: sql/Review.sql

============================================================
ALL SQL FILES GENERATED SUCCESSFULLY IN sql/ FOLDER!
============================================================
```

### Step 3: Create and populate database

**First, create the database:**
```sql
CREATE DATABASE FINAL_URP;
USE FINAL_URP;
```

**Important Note:** The generated SQL files contain only INSERT statements. You need to create the table schemas first. If you have CREATE TABLE statements in separate files in the `sql/` folder, run those first. Otherwise, you'll need to create the tables manually or add CREATE TABLE statements to the beginning of each generated file.

**Load data from generated SQL files:**
```bash
mysql -u root -p FINAL_URP < sql/Department.sql
mysql -u root -p FINAL_URP < sql/Member.sql
mysql -u root -p FINAL_URP < sql/Category.sql
mysql -u root -p FINAL_URP < sql/Paper.sql
mysql -u root -p FINAL_URP < sql/Keyword.sql
mysql -u root -p FINAL_URP < sql/PaperAuthor.sql
mysql -u root -p FINAL_URP < sql/PaperKeyword.sql
mysql -u root -p FINAL_URP < sql/Comment.sql
mysql -u root -p FINAL_URP < sql/Review.sql
```

Or run them all at once from within MySQL:
```sql
SOURCE sql/Department.sql;
SOURCE sql/Member.sql;
SOURCE sql/Category.sql;
SOURCE sql/Paper.sql;
SOURCE sql/Keyword.sql;
SOURCE sql/PaperAuthor.sql;
SOURCE sql/PaperKeyword.sql;
SOURCE sql/Comment.sql;
SOURCE sql/Review.sql;
```

### Step 4: Update database passwords

**IMPORTANT:** Before running test notebooks, update the MySQL password in:
- `src/concurrency solution.ipynb` - Change `password="<password>"`
- `src/testcases.ipynb` - Change `password="//PASSWORD//"`

**Note:** `src/Transactions.ipynb` uses SQLite and does not require password configuration.

### Step 5: Run functional tests

Open and execute the following notebooks in order:

1. **`src/Transactions.ipynb`** - Complete ACID + Indexing demonstration
   - Uses SQLite in-memory database (no setup required)
   - Section 1: Atomicity (rollback vs commit)
   - Section 2: Consistency (constraint validation)
   - Section 3: Isolation (transaction isolation)
   - Section 4: Durability (persistent changes)
   - Section 5: Indexing (performance comparison with 5,000 rows)

2. **`src/concurrency solution.ipynb`** - Test isolation levels (MySQL)
   - READ UNCOMMITTED (dirty reads)
   - READ COMMITTED (prevents dirty reads)
   - REPEATABLE READ (consistent reads)
   - SERIALIZABLE (maximum isolation)

3. **`src/testcases.ipynb`** - Test CRUD and complex queries (MySQL)
   - INSERT/SELECT/UPDATE/DELETE operations
   - JOIN, SUBQUERY, and AGGREGATE queries

### Step 6: Backup database

```bash
mysqldump -u root -p FINAL_URP > backup/final_urp_backup.sql
```

Or use the automated backup script: `backup/backup_final_urp.bat`

---

## 4. Building the Database

### 4.1 Generate SQL files

**IMPORTANT:** Run this first to generate all `.sql` files:

```bash
jupyter notebook
```

Open and execute `src/DBMS_Final_TeamA.ipynb`. This notebook:
- Reads data from `data/dblp-v10.csv`
- Processes and cleans the research paper dataset
- Extracts 4,600+ unique real authors from the papers
- Creates 4,900+ members (real authors + 300 additional university members)
- Generates 1,500 papers with realistic metadata
- Creates relationships: PaperAuthor, Keywords, Reviews, Comments
- Automatically creates `sql/` directory if it doesn't exist
- Exports all data to `.sql` files in the `sql/` folder

**Generated SQL files in `sql/` folder:**
- `Department.sql` - 20 departments
- `Member.sql` - 4,900+ members (70% students, 30% faculty)
- `Category.sql` - 4 paper categories
- `Paper.sql` - 1,500 research papers
- `Keyword.sql` - Extracted keywords from paper titles
- `PaperKeyword.sql` - Many-to-many relationship
- `PaperAuthor.sql` - Links real authors to their papers
- `Comment.sql` - 1-10 comments per paper
- `Review.sql` - 1-3 reviews per paper (faculty only)

### 4.2 Create database

```sql
CREATE DATABASE FINAL_URP;
USE FINAL_URP;
```

### 4.3 Execute SQL files to create schema and populate data

**Important:** The generated SQL files contain only INSERT statements. You must create table schemas first.

**Option 1 - If you have CREATE TABLE scripts:**
Run your CREATE TABLE scripts before loading data.

**Option 2 - Add CREATE statements to generated files:**
Manually add CREATE TABLE statements to the beginning of each generated SQL file.

**Then load the data:**

```bash
mysql -u root -p FINAL_URP < sql/Department.sql
mysql -u root -p FINAL_URP < sql/Member.sql
mysql -u root -p FINAL_URP < sql/Category.sql
mysql -u root -p FINAL_URP < sql/Paper.sql
mysql -u root -p FINAL_URP < sql/Keyword.sql
mysql -u root -p FINAL_URP < sql/PaperAuthor.sql
mysql -u root -p FINAL_URP < sql/PaperKeyword.sql
mysql -u root -p FINAL_URP < sql/Comment.sql
mysql -u root -p FINAL_URP < sql/Review.sql
```

---

## 5. SQL Scripts Overview

### 5.1 Generated SQL files

Each `.sql` file is **generated by `DBMS_Final_TeamA.ipynb`** and contains INSERT statements:

- **Department.sql** - 20 academic departments
- **Member.sql** - 4,900+ members (real authors from dataset + additional members)
  - 70% Students, 30% Faculty
  - Real author names from DBLP dataset
- **Category.sql** - 4 paper categories (Reviewed, Unreviewed, Survey, Conference)
- **Paper.sql** - 1,500 research papers with titles, abstracts, year, PDF links
- **Keyword.sql** - Keywords extracted from paper titles (5+ letter words)
- **PaperKeyword.sql** - Many-to-many relationship (up to 5 keywords per paper)
- **PaperAuthor.sql** - Links papers to their real authors
- **Comment.sql** - 1-10 comments per paper from members
- **Review.sql** - 1-3 reviews per paper from faculty only (score 1-10 + feedback)  

### 5.2 CRUD operations

**Implemented in:** `src/testcases.ipynb`

**Requirements:** Update the database password in the notebook before running.

Tests include:

**INSERT Operations:**
- Insert new members with validation
- Test duplicate email prevention

**SELECT Operations:**
- Query members by email
- Verify inserted data

**UPDATE Operations:**
- Update member role
- Update member student_id

**DELETE Operations:**
- Delete test members
- Verify deletion with COUNT queries

Each operation includes before/after verification queries and prints results to console.

### 5.3 Complex queries

**Implemented in:** `src/testcases.ipynb`

**JOIN Queries:**
- Members with their departments (LIMIT 5)
- Members filtered by specific department ID

**SUBQUERY Examples:**
- Find members in the largest department (by member count)
- Find members in the smallest department

**AGGREGATE Queries:**
- Count total members per department (GROUP BY dept_id)
- Count faculty members per department (filtered by role)

All queries display results with proper formatting and LIMIT clauses for readability.

### 5.4 Indexing and performance optimization

**Included in:** `src/Transactions.ipynb` (Section 5 of the notebook)

The Transactions notebook includes a comprehensive indexing demonstration:

**Setup:**
- Creates a test table `LargeTable` with 5,000 rows
- Each row has: id, name, score (0-99), created_at timestamp

**Performance Comparison:**

**Before Indexing:**
- Queries use full table scan
- Records execution time for baseline

**After Indexing:**
- Creates index on `score` column: `CREATE INDEX idx_score ON LargeTable(score)`
- Queries use index for faster lookups
- Records improved execution time

**Test Query:**
- Query: `SELECT * FROM LargeTable WHERE score = 50`
- Shows number of rows returned and execution time
- Handles duplicate index creation gracefully

**Key Indexes for Production:**

For the main FINAL_URP database, consider adding these indexes:

```sql
CREATE INDEX idx_keyword_keyword ON Keyword(keyword);
CREATE INDEX idx_paperkeyword_paperid ON PaperKeyword(paper_id);
CREATE INDEX idx_paperkeyword_keywordid ON PaperKeyword(keyword_id);
CREATE INDEX idx_member_email ON Member(email);
CREATE INDEX idx_member_deptid ON Member(dept_id);
CREATE INDEX idx_paper_deptid ON Paper(dept_id);
CREATE INDEX idx_review_paperid ON Review(paper_id);
CREATE INDEX idx_comment_paperid ON Comment(paper_id);
```

**Note:** The Transactions.ipynb uses SQLite for demonstration purposes in Colab environment, making it easy to run without external database setup.

---

## 6. Transaction Management (ACID)

**Notebook:** `src/Transactions.ipynb`

**Environment:** Uses SQLite in-memory database for easy demonstration in Colab (no MySQL setup required)

This comprehensive notebook demonstrates all ACID properties and indexing in a single unified environment:

**Database Setup:**
- Creates complete schema matching FINAL_URP structure
- Tables: Department, Member, Category, Paper, PaperAuthor, Keyword, PaperKeyword, Comment, Review
- Populates with sample data for testing

**Section 1: Atomicity**
- Temporary update to Paper title (rollback)
- Permanent update to Paper title (commit)
- Shows all-or-nothing transaction behavior
- Prints before/after states for each operation

**Section 2: Consistency**
- Validates table constraints (PK, FK, NOT NULL, CHECK, UNIQUE)
- Temporary insert into Comment table (rollback)
- Permanent insert with duplicate check (commit)
- Maintains referential integrity throughout
- Uses COUNT queries to verify consistency

**Section 3: Isolation**
- Demonstrates transaction isolation with BEGIN TRANSACTION
- Temporary score update in Review table (rollback)
- Permanent score update (commit)
- Shows how uncommitted changes are isolated
- SQLite uses serialized transactions by default

**Section 4: Durability**
- Temporary insert into Member table (rollback)
- Permanent insert with unique email timestamp (commit)
- Verifies data persists after commit
- Uses COUNT queries to validate changes

**Section 5: Indexing Performance**
- Creates LargeTable with 5,000 rows
- Tests query performance without index
- Creates index on score column
- Tests query performance with index
- Demonstrates significant performance improvement

**Output Format:**
Each section prints:
- Clear section headers (e.g., "=== Atomicity Example ===")
- Before/after states for every operation
- Query results and counts
- Execution times for indexing tests

**Key Advantages:**
- Self-contained: No external database required
- Runs in Colab: Easy to share and test
- Complete demonstration: All ACID properties + indexing in one notebook
- Clear output: Easy to understand results

---

## 7. Concurrency Control

**Notebook:** `src/concurrency solution.ipynb`

**Requirements:** Update the database password (`<password>`) in all connection strings before running.

Demonstrates four isolation levels with concurrent transactions:

**READ UNCOMMITTED**
- Two connections with READ UNCOMMITTED isolation
- Transaction 1: Updates Review score (uncommitted)
- Transaction 2: Reads dirty value before rollback
- Shows dirty read phenomenon
- Includes rollback and commit examples

**READ COMMITTED**
- Two connections with READ COMMITTED isolation  
- Transaction 1: Updates Review score (uncommitted)
- Transaction 2: Cannot see uncommitted changes (prevents dirty reads)
- Demonstrates proper isolation behavior

**REPEATABLE READ**
- Two connections with REPEATABLE READ isolation
- Transaction 1: Reads initial value, makes temporary update
- Transaction 2: Reads during Transaction 1 (sees consistent value)
- Prevents non-repeatable reads
- Shows rollback and commit behavior

**SERIALIZABLE**
- Single connection demonstration
- Highest isolation level
- Temporary update with rollback
- Permanent update with commit
- Prevents all concurrency anomalies

Each test:
- Resets Review score to 3 for predictable results
- Shows uncommitted changes
- Demonstrates rollback behavior
- Makes permanent changes with commit
- Uses `flush=True` for real-time output  

---

## 8. Automated Testing (CRUD + Queries)

**Notebook:** `src/testcases.ipynb`

**Requirements:** Update database password (`//PASSWORD//`) before running.

**Test Structure:**

The notebook uses a `run_test()` helper function that:
- Connects to database for each test
- Executes action queries (INSERT/UPDATE/DELETE)
- Executes fetch queries (SELECT) for verification
- Prints results with proper formatting
- Handles errors with rollback
- Closes connections after each test

**CRUD Tests:**
- **INSERT Test 1 & 2:** Insert two new members (User1, User2)
- **READ Test:** Verify member insertion by email
- **UPDATE Test 1:** Change member role to 'Student'
- **UPDATE Test 2:** Update student_id field
- **DELETE Test 1 & 2:** Remove test members and verify with COUNT

**Complex Query Tests:**
- **JOIN Test 1:** Members with departments (LIMIT 5)
- **JOIN Test 2:** Members in Department ID 1 (LIMIT 5)
- **SUBQUERY Test 1:** Members in largest department
- **SUBQUERY Test 2:** Members in smallest department
- **AGGREGATE Test 1:** Count members per department
- **AGGREGATE Test 2:** Count faculty per department

**Output:**
Each test prints:
- Test name
- Query execution status
- Result rows (dictionary format)
- Success/failure messages
- Error details if applicable

**Execution:**
```python
if __name__ == "__main__":
    test_crud()              # Run all CRUD tests
    test_complex_queries()   # Run all complex queries
```

This notebook serves as the **official automated test suite**.

---

## 9. Backup and Recovery

### 9.1 Create a manual backup

```bash
mysqldump -u root -p FINAL_URP > backup/final_urp_backup.sql
```

### 9.2 Restore the database

```sql
DROP DATABASE FINAL_URP;
CREATE DATABASE FINAL_URP;
```

Then:

```bash
mysql -u root -p FINAL_URP < backup/final_urp_backup.sql
```

### 9.3 Automated Daily Backup (Windows)

Batch script: `backup/backup_final_urp.bat`

```batch
@echo off
set TIMESTAMP=%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%_%TIME:~0,2%-%TIME:~3,2%
mysqldump -u root -pYourPassword FINAL_URP > "C:\backup\final_urp_%TIMESTAMP%.sql"
echo Backup completed at %TIMESTAMP%
```

Use Windows Task Scheduler to automate.

---