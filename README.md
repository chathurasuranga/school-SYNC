# SchoolSync

SchoolSync is a comprehensive School Management System designed to streamline administrative tasks, student grading, and class management. Built with PHP and MySQL, it offers a user-friendly interface for administrators and teachers to manage school operations efficiently.

## Features

- **Admin Dashboard**: Centralized hub for managing students, teachers, and classes.
- **Grading System**: Record and calculate student grades including GPA and letter grades.
- **Student Management**: Enroll students, view profiles, and track academic progress.
- **Teacher Management**: Assign classes and manage teacher profiles.
- **Class Management**: Organize classes and official reports.
- **PDF Reports**: Generate official reports and student grade sheets.
- **Secure Authentication**: distinct login roles for various users.

## Tech Stack

- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL
- **Environment**: XAMPP (Apache)

## Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/chathurasuranga/school-SYNC.git
    cd school-SYNC
    ```

2.  **Database Setup**
    - Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
    - Create a new database named `schoolsync`.
    - Import the `schoolsync.sql` file provided in the root directory.

3.  **Application Config**
    - Ensure your Apache server is running via XAMPP control panel.
    - Move the project folder to `htdocs` (if not already there).
    - Configure database connection settings in `db.php` if necessary.

4.  **Run the Application**
    - Open your browser and navigate to `http://localhost/schoolsync`.

## Contributing

Contributions are welcome! Please fork the repository and create a pull request for any feature enhancements or bug fixes.
