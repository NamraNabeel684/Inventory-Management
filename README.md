#  Inventory Management System
## Technologies Used
### Frontend
- HTML5
- CSS3
- JavaScript
### Backend
- PHP
### Database
- MySQL
### Development Tools
- Visual Studio Code
- PHP
- MySQL
## Project Features
The system includes the following features:
- User registration and login
- Secure user authentication using PHP sessions
- Dashboard with inventory overview
- Display of total products
- Display of total categories
- Low stock product monitoring
- Recent inventory activity displayed on the dashboard
- Add new product functionality
- Edit/delete product details
- Product category management
- Search products by name
- Filter products 
- Inventory and stock management
- Inventory activity tracking
- User logout functionality
- Responsive and user-friendly interface
- 
## Project Structure
twisted-threads-inventory/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   ├── js/
│   │   └── script.js
│   │
│   └── images/
│       └── logo.png
│
├── config/
│   └── database.php
│
├── includes/
│   ├── auth_check.php
│   ├── header.php
│   └── footer.php
│
├── uploads/
│   └── Product images are stored here
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── products.php
├── add_product.php
├── edit_product.php
├── delete_product.php
├── categories.php
├── inventory.php
└── activity.php
## Database Setup
Create a MySQL database named:
CREATE DATABASE twisted_threads_inventory;
Then select the database:
USE twisted_threads_inventory;
Create the required tables for:
Users
Categories
Products
Inventory Activity
Example structure:
CREATE TABLE inventory_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity_type VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);
## How to Run the Project
1. Install **PHP, MySQL, and Visual Studio Code**.
2. Open the `twisted-threads-inventory` folder in VS Code.
3. Start the **MySQL server**.
4. Create the database and run the provided SQL queries. Make sure the database name matches `config/database.php`.
5. Run the following command in the VS Code terminal:
php -S localhost:8000
6. Open `http://localhost:8000` in your browser.




