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

## 3. Building the Database

### 3.1 Generate SQL files

**IMPORTANT:** Run this first to generate all `.sql` files:

```bash
jupyter notebook
```

Open and execute `src/DBMS_Final_TeamA.ipynb`. 

**Note:** The SQL files are already generated and stored in the `sql/` folder. You only need to run this notebook if you want to regenerate the data with different random seeds or modify the data generation logic.

This notebook:
- Reads data from `data/dblp-v10.csv`
- Processes and cleans the research paper dataset
- Extracts 4,600+ unique real authors from the papers
- Creates 4,900+ members (real authors + 300 additional university members)
- Generates 1,500 papers with realistic metadata
- Creates relationships: PaperAuthor, Keywords, Reviews, Comments
- Automatically creates `sql/` directory if it doesn't exist
- Exports all data to `.sql` files in the `sql/` folder

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

### 3.2 Create database

```sql
CREATE DATABASE FINAL_URP;
USE FINAL_URP;
```

### 3.3 Execute SQL files to create schema and populate data

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

---

## 4. SQL Scripts Overview

### 4.1 Generated SQL files

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

### 4.2 CRUD operations

**Implemented in:** `src/testcases.ipynb`

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

### 4.3 Complex queries

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

### 4.4 Indexing and performance optimization

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

## 5. Running the Project

### 5.1 Setup environment

```bash
# Activate virtual environment
source .venv/bin/activate  # Mac/Linux
.venv\Scripts\activate     # Windows

# Launch Jupyter Notebook
jupyter notebook
```

### 5.2 Update database passwords

**IMPORTANT:** Before running test notebooks, update the MySQL password in:
- `src/concurrency solution.ipynb` - Change `password="<password>"`
- `src/testcases.ipynb` - Change `password="//PASSWORD//"`

**Note:** `src/Transactions.ipynb` uses SQLite and does not require password configuration.

### 5.3 Run test notebooks

Execute the following notebooks in order:

#### 5.3.1 Transactions.ipynb - ACID Properties & Indexing

**Notebook:** `src/Transactions.ipynb`

**Environment:** Uses SQLite in-memory database (no MySQL setup required)

This comprehensive notebook demonstrates all ACID properties and indexing:

**Section 1: Atomicity**
- Temporary update to Paper title (rollback)
- Permanent update to Paper title (commit)
- Shows all-or-nothing transaction behavior

**Section 2: Consistency**
- Validates table constraints (PK, FK, NOT NULL, CHECK, UNIQUE)
- Temporary insert into Comment table (rollback)
- Permanent insert with duplicate check (commit)
- Maintains referential integrity throughout

**Section 3: Isolation**
- Demonstrates transaction isolation with BEGIN TRANSACTION
- Temporary score update in Review table (rollback)
- Permanent score update (commit)
- SQLite uses serialized transactions by default

**Section 4: Durability**
- Temporary insert into Member table (rollback)
- Permanent insert with unique email timestamp (commit)
- Verifies data persists after commit

**Section 5: Indexing Performance**
- Creates LargeTable with 5,000 rows
- Tests query performance without index
- Creates index on score column
- Tests query performance with index
- Demonstrates significant performance improvement

#### 5.3.2 Concurrency Control

**Notebook:** `src/concurrency solution.ipynb`

**Requirements:** MySQL database with password configured

Demonstrates four isolation levels with concurrent transactions:

**READ UNCOMMITTED**
- Two connections with READ UNCOMMITTED isolation
- Transaction 1: Updates Review score (uncommitted)
- Transaction 2: Reads dirty value before rollback
- Shows dirty read phenomenon

**READ COMMITTED**
- Two connections with READ COMMITTED isolation  
- Transaction 1: Updates Review score (uncommitted)
- Transaction 2: Cannot see uncommitted changes (prevents dirty reads)

**REPEATABLE READ**
- Two connections with REPEATABLE READ isolation
- Transaction 1: Reads initial value, makes temporary update
- Transaction 2: Reads during Transaction 1 (sees consistent value)
- Prevents non-repeatable reads

**SERIALIZABLE**
- Single connection demonstration
- Highest isolation level
- Temporary update with rollback
- Permanent update with commit
- Prevents all concurrency anomalies

#### 5.3.3 CRUD Operations & Complex Queries

**Notebook:** `src/testcases.ipynb`

**Requirements:** MySQL database with password configured

The notebook uses a `run_test()` helper function that:
- Connects to database for each test
- Executes action queries (INSERT/UPDATE/DELETE)
- Executes fetch queries (SELECT) for verification
- Prints results with proper formatting
- Handles errors with rollback

**CRUD Tests:**
- INSERT Test 1 & 2: Insert two new members
- READ Test: Verify member insertion by email
- UPDATE Test 1 & 2: Change member role and student_id
- DELETE Test 1 & 2: Remove test members and verify with COUNT

**Complex Query Tests:**
- JOIN Test 1 & 2: Members with departments
- SUBQUERY Test 1 & 2: Members in largest/smallest department
- AGGREGATE Test 1 & 2: Count members and faculty per department

---

## 6. Transaction Management (ACID) - Detailed Explanation

**Implementation:** `src/Transactions.ipynb`

**Database Setup:**
- Creates complete schema matching FINAL_URP structure
- Tables: Department, Member, Category, Paper, PaperAuthor, Keyword, PaperKeyword, Comment, Review
- Populates with sample data for testing

**Atomicity**
Ensures that all operations in a transaction complete successfully or none do. The notebook demonstrates:
- A temporary update that gets rolled back (all-or-nothing)
- A permanent update that gets committed
- Verification that partial updates never exist in the database

**Consistency**
Ensures the database remains in a valid state before and after transactions. The notebook demonstrates:
- Constraint validation (PRIMARY KEY, FOREIGN KEY, NOT NULL, CHECK, UNIQUE)
- Temporary data insertion that maintains constraints
- Rollback that returns database to consistent state
- Permanent insertion with duplicate prevention

**Isolation**
Ensures concurrent transactions don't interfere with each other. The notebook demonstrates:
- Transaction 1 makes changes that are isolated from other queries
- Uncommitted changes are not visible to other transactions
- Only committed changes become visible
- SQLite uses serialized isolation by default

**Durability**
Ensures committed transactions persist even after system failure. The notebook demonstrates:
- Temporary changes that disappear after rollback
- Permanent changes that persist after commit
- Verification that data remains after transaction completes

---

## 7. Concurrency Control - Detailed Explanation

**Implementation:** `src/concurrency solution.ipynb`

**Purpose:** Demonstrates how different isolation levels handle concurrent database access

**Isolation Levels Tested:**

**READ UNCOMMITTED** (Lowest isolation)
- Allows dirty reads
- Transaction can read uncommitted changes from other transactions
- Demonstrates: One transaction reads data modified but not committed by another
- Problem shown: Data may be rolled back, making the read invalid

**READ COMMITTED** (Default in many databases)
- Prevents dirty reads
- Transaction only sees committed data
- Demonstrates: Uncommitted changes are invisible to other transactions
- Problem prevented: No reading of data that might be rolled back

**REPEATABLE READ**
- Prevents dirty reads and non-repeatable reads
- Data read once stays consistent throughout the transaction
- Demonstrates: Multiple reads of same data return same results
- Problem prevented: Other transactions cannot modify data you've read

**SERIALIZABLE** (Highest isolation)
- Prevents all concurrency anomalies
- Transactions execute as if they were serial (one after another)
- Demonstrates: Complete isolation from concurrent transactions
- Trade-off: Lower performance due to strict locking

**Each test:**
- Resets Review score to 3 for predictable results
- Shows uncommitted changes
- Demonstrates rollback behavior
- Makes permanent changes with commit
- Uses `flush=True` for real-time output

---

## 8. Automated Testing - Detailed Explanation

**Implementation:** `src/testcases.ipynb`

**Test Framework:**

The notebook implements a custom `run_test()` helper function that provides:
- Automatic database connection management
- Query execution with parameter binding
- Result fetching and display
- Error handling with automatic rollback
- Clean connection closure

**CRUD Test Coverage:**

**INSERT Operations:**
- Tests basic insertion with all required fields
- Validates unique constraints (duplicate emails)
- Verifies foreign key relationships
- Confirms data integrity

**SELECT Operations:**
- Tests basic retrieval by primary key
- Tests retrieval by unique fields (email)
- Validates query results match inserted data
- Tests multiple row retrieval

**UPDATE Operations:**
- Tests single field updates
- Tests multiple field updates
- Verifies changes with SELECT queries
- Ensures only target rows are modified

**DELETE Operations:**
- Tests deletion by primary key
- Tests deletion by other criteria
- Verifies deletion with COUNT queries
- Ensures referential integrity is maintained

**Complex Query Test Coverage:**

**JOIN Queries:**
- Tests INNER JOIN between Member and Department
- Validates relationship data is correctly linked
- Tests filtered JOINs with WHERE clauses
- Uses LIMIT to manage result size

**SUBQUERY Tests:**
- Tests scalar subqueries (single value)
- Tests subqueries with aggregation (MAX, MIN, COUNT)
- Demonstrates correlated vs non-correlated subqueries
- Validates complex nested queries

**AGGREGATE Queries:**
- Tests COUNT, SUM, AVG functions
- Tests GROUP BY with multiple columns
- Tests HAVING clause for filtered aggregates
- Validates aggregate results match expected values

**Output Format:**
Each test prints:
- Test name with clear identification
- Query execution status (success/failure)
- Result rows in dictionary format for readability
- Success messages or error details
- Row counts for verification

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
