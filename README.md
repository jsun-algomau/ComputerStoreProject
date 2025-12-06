Computer Store Web Application

Project Description

This project is a web-based Computer Store application developed using PHP and MySQL.  
The system allows users to browse computer-related products, view detailed product information, add items to a shopping cart, place orders, and view their order history.

Key features include:
Product listing with filtering and sorting
Individual product detail pages
Shopping cart management (add, update, remove items)
User authentication (register, login, logout)
Checkout process and order storage
Order history page for logged-in users
Responsive UI using Bootstrap
Local database integration with MySQL

This project demonstrates full-stack web development concepts including server-side scripting, database design, session management, and basic e-commerce workflow.

---

Setup Instructions

Follow the steps below to run the project locally using XAMPP:

 1. Install Required Software
Install **XAMPP** (Apache & MySQL enabled)
Install **Git** (optional, for repository cloning)
A web browser (Chrome, Firefox, etc.)

2. Clone or Download the Project
Place the project folder inside:
C:\xampp\htdocs\



For example:
C:\xampp\htdocs\online-store



### 3. Database Setup
1. Open **phpMyAdmin**
2. Create a new database (e.g. `computer_store`)
3. Import the provided SQL file:
yourDatabase.sql



### 4. Configure Database Connection
Edit the following file:
db/conn.php




Update it with your local database credentials:
php
$conn = mysqli_connect("localhost", "root", "", "computer_store");

5. Start the Application
Start Apache and MySQL in XAMPP

Open a browser and go to:

arduino

http://localhost/online-store

Author

Name: Jie Sun
Student ID: 219569760
Course: COSC-2956-F02 - Internet Tools
