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

### 2.2 Create virtual environment

```bash
python -m venv .venv

# Windows:
.venv\Scripts\activate

# Mac/Linux:
source .venv/bin/activate
```

### 2.3 Install dependencies

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

### 3.1 Create database

```sql
CREATE DATABASE FINAL_URP;
USE FINAL_URP;
```

### 3.2 Create schema (tables)

Run the SQL files inside MySQL:

```sql
SOURCE Department.sql;
SOURCE Member.sql;
SOURCE Category.sql;
SOURCE Paper.sql;
SOURCE Keyword.sql;
SOURCE PaperAuthor.sql;
SOURCE PaperKeyword.sql;
SOURCE Comment.sql;
SOURCE Review.sql;
```

Or via terminal:

```bash
mysql -u root -p FINAL_URP < sql/Department.sql
```

### 3.3 Populate tables

The `.sql` files in the `sql/` folder contain all generated **INSERT** data.  
They load ≥ 1000 rows per table based on the `dblp-v10.csv` dataset.

Load them in the same order as schema creation.

---

## 4. SQL Scripts Overview

### 4.1 Schema files
Each `.sql` file corresponds to a table:

- Department  
- Member  
- Category  
- Paper  
- Keyword & PaperKeyword  
- PaperAuthor  
- Comment  
- Review  

### 4.2 CRUD operations

Implemented in:

- `src/testcases.ipynb`
- `src/DBMS_Final_TeamA.ipynb`

Includes:

- Insert  
- Select  
- Update  
- Delete  

Notebook output logs verify each operation.

### 4.3 Complex queries

Located in:

- `src/testcases.ipynb`
- `src/DBMS_Final_TeamA.ipynb`

Includes:

- JOIN queries  
- Subqueries  
- Aggregations  
- Department summaries  
- Keyword frequency  
- Top authors and top-rated papers  

All queries include explanation + result output.

### 4.4 Indexing and performance optimization

Relevant notebook: `DBMS_Final_TeamA.ipynb`

Example indexes:

```sql
CREATE INDEX idx_keyword_keyword ON Keyword(keyword);
CREATE INDEX idx_paperkeyword_paperid ON PaperKeyword(paper_id);
CREATE INDEX idx_paperkeyword_keywordid ON PaperKeyword(keyword_id);
```

Performance tested using:

```sql
EXPLAIN SELECT ...
```

---

## 5. Transaction Management (ACID)

Notebook: `src/Transactions.ipynb`

Covers:

**Atomicity**
- Rollback vs commit  

**Consistency**
- Constraint enforcement  
- FK validation  

**Isolation**
- READ COMMITTED example  
- Visibility testing  

**Durability**
- Updates remain after commit and reconnect  

All results logged in the notebook.

---

## 6. Concurrency Control

Notebook: `src/concurrency solution.ipynb`

Demonstrates:

- Dirty reads  
- Non-repeatable reads  
- Lost updates  
- Correct use of isolation levels  
- Concurrent session behavior  

---

## 7. Automated Testing (CRUD + Queries)

Notebook: `src/testcases.ipynb`

Tests include:

**CRUD Tests**
- Inserts  
- Select verification  
- Updates  
- Deletes  

**Complex Query Tests**
- JOIN queries  
- Subqueries  
- Aggregates  
- Department-level analysis  

Each test prints:

- Operation name  
- Execution status  
- Query output  

This notebook serves as the **official test report**.

---

## 8. Backup and Recovery

### 8.1 Create a manual backup

```bash
mysqldump -u root -p FINAL_URP > backup/final_urp_backup.sql
```

### 8.2 Restore the database

```sql
DROP DATABASE FINAL_URP;
CREATE DATABASE FINAL_URP;
```

Then:

```bash
mysql -u root -p FINAL_URP < backup/final_urp_backup.sql
```

### 8.3 Automated Daily Backup (Windows)

Batch script: `backup/backup_final_urp.bat`

```batch
@echo off
set TIMESTAMP=%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%_%TIME:~0,2%-%TIME:~3,2%
mysqldump -u root -pYourPassword FINAL_URP > "C:\backup\final_urp_%TIMESTAMP%.sql"
echo Backup completed at %TIMESTAMP%
```

Use Windows Task Scheduler to automate.

---

## 9. Running the Project

### Start virtual environment

```bash
source .venv/bin/activate
```

### Launch Jupyter Notebook

```bash
jupyter notebook
```

Open:

- `src/DBMS_Final_TeamA.ipynb` (database setup)  
- `src/Transactions.ipynb` (ACID)  
- `src/concurrency solution.ipynb` (concurrency)  
- `src/testcases.ipynb` (tests)  

---

## 10. Requirement Checklist

| Requirement | Included |
|-------------|----------|
| SQL schema creation | Yes |
| Data population | Yes |
| CRUD operations | Yes |
| Complex queries | Yes |
| Indexes & performance analysis | Yes |
| ACID transactions | Yes |
| Concurrency / isolation | Yes |
| Backup & recovery | Yes |
| Automated tests | Yes |
| Comprehensive documentation | Yes |

---

## 11. Team Members

- Bharat Manivannan  
- Jiayang Wang  
- Sai Sneha Kakarla  
- Jaswanthi Mandalapu