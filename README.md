# 🎓 StudyMate — NSBM Peer Learning & Academic Collaboration Platform

[![NSBM University](https://img.shields.io/badge/NSBM-Green%20University-006837?style=for-the-badge&logo=graduation-cap&logoColor=white)](https://www.nsbm.ac.lk/)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

> **NSBM Faculty of Computing — 1st Year Under-Graduate Project**  
> *StudyMate* is a full-stack web platform designed to foster peer-to-peer collaborative learning, academic resource sharing, module-specific Q&A discussions, and study group formation for NSBM undergraduates.

---

## ✨ Features Breakdown

### 👨‍🎓 **Student Module**
* **Resource Hub**: Search and filter lecture notes, slide decks, past exam papers, and library e-journals by computing module.
* **Resource Uploads**: Upload study materials (`.pdf`, `.docx`, `.pptx`, `.zip`) with custom tags for admin review.
* **Rating & Discussion**: Rate resources (1–5 stars) and leave constructive comments on peer-uploaded materials.
* **Academic Q&A Forum**: Post module-specific questions, share code solutions, and help classmates troubleshoot coursework.
* **Study Groups**: Create or join dedicated module study groups, view member rosters, and coordinate exam prep.

### 🛡️ **Admin Moderation Panel**
* **Live Dashboard Metrics**: Track active students, pending uploads, approved resources, and group statistics.
* **Material Approval Workflow**: Review, approve, reject, or delete submitted learning materials before publication.
* **Subject & Module Management**: Full CRUD controls to add and organize computing modules (e.g. *Programming Fundamentals*, *Data Communication & Security*, *Object-Oriented Programming with Java*).
* **User Management**: Directory to search, activate, block, or delete student accounts.
* **Forum Moderation**: Inspect, hide, or restore questions and answers to maintain community standards.

---

## 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Frontend** | HTML5, CSS3 (Custom Glassmorphism Design System), JavaScript, Bootstrap 5.3, FontAwesome 6 |
| **Typography** | Plus Jakarta Sans (Google Fonts) |
| **Backend** | PHP 8.x (Procedural, Prepared MySQLi Statements) |
| **Database** | MySQL / MariaDB (Port 3306/3307) |
| **Local Server** | XAMPP / Apache Web Server |

---

## 🚀 Quick Setup Instructions (XAMPP)

1. **Clone or Download Repository**:
   ```bash
   git clone https://github.com/Sachithraoshan/studymate.git
   ```
   Place the project in your XAMPP web root directory:
   `C:\xampp\htdocs\studymate`

2. **Start Web Server & Database**:
   - Open **XAMPP Control Panel**.
   - Start **Apache** and **MySQL** services.

3. **Import Database Schema**:
   - Open **phpMyAdmin**: `http://localhost/phpmyadmin/`
   - Create a new database named `studymate_db` (or import directly).
   - Go to **Import** tab → Choose file `database/studymate.sql` → Click **Go**.

4. **Verify Database Connection**:
   - Open `config/db.php` and verify your MySQL credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'studymate_db');
     ```

5. **Launch Application**:
   - Open browser and navigate to: [http://localhost/studymate/](http://localhost/studymate/)

---

## 🔑 Demo Credentials

| Role | Email Address | Password | Access URL |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@studymate.com` | `admin123` | `http://localhost/studymate/login.php` |
| **Student** | `student@studymate.com` | `student123` | `http://localhost/studymate/login.php` |

---

## 📁 Repository Structure

```
studymate/
├── admin/                  # Admin moderation panel & management scripts
│   ├── approve_materials.php
│   ├── dashboard.php
│   ├── manage_subjects.php
│   ├── manage_users.php
│   └── moderate_discussions.php
├── user/                   # Student portal pages
│   ├── ask_question.php
│   ├── create_group.php
│   ├── dashboard.php
│   ├── group_detail.php
│   ├── materials.php
│   ├── question_detail.php
│   ├── questions.php
│   ├── study_groups.php
│   └── upload_material.php
├── assets/                 # Custom CSS design system & JavaScript
│   ├── css/style.css
│   └── js/script.js
├── config/                 # DB configuration
│   └── db.php
├── database/               # SQL dump schema and seed data
│   └── studymate.sql
├── includes/               # Shared Header, Footer, and Auth Guard
│   ├── auth.php
│   ├── footer.php
│   └── header.php
├── uploads/materials/      # Uploaded academic resources directory
├── index.php               # Landing Page
├── login.php               # Shared Login
├── register.php            # Student Registration
└── README.md
```

---

## 🤝 Reference Sources & Acknowledgments
- **NSBM Green University Library E-Journals**: [library.nsbm.ac.lk](https://library.nsbm.ac.lk/)
- **NSBM Computing Course Content**: [nsbm.ac.lk](https://www.nsbm.ac.lk/)

---

## 📄 License
This project is open-source and created for educational purposes under the [MIT License](LICENSE).
