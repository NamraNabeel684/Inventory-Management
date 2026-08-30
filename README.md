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
- Dashboard with inventory overview
- Display of statistics
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
## Database Setup
1. Create a MySQL database:
CREATE DATABASE twisted_threads_inventory;
2. Select the database:
USE twisted_threads_inventory;
3. Create the required tables for:
* Users
* Categories
* Products
* Inventory Activity
Example table structure for `inventory_activity`:
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
Make sure all required tables are created before running the project. The database name should also match the configuration in `config/database.php`.
## How to Run the Project
1. Install **PHP, MySQL, and Visual Studio Code**.
2. Open the `twisted-threads-inventory` folder in VS Code.
3. Start the **MySQL server**.
4. Create the database and run the provided SQL queries. Make sure the database name matches `config/database.php`.
5. Run the following command in the VS Code terminal:
php -S localhost:8000
6. Open `http://localhost:8000` in your browser.




