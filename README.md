<p align="center">
  <img src="assets/Thameen-Logo/4.png" alt="Thameen Logo" width="200"/>
</p>

# 🏺 Thameen Luxury Jewelry E-Commerce Platform

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white) ![Apache](https://img.shields.io/badge/Server-Apache-D22128?logo=apache&logoColor=white) ![License](https://img.shields.io/badge/License-MIT-green) ![Built With](https://img.shields.io/badge/Built%20With-HTML%2C%20CSS%2C%20JS-blue)


A modern, responsive e-commerce website for luxury jewelry sales, built with PHP and MySQL. This platform provides a complete shopping experience with user authentication, product management, shopping cart functionality, and order processing.

## ✨ Features

### 🛍️ Customer Features

- **Product Catalog**: Browse and view detailed jewelry products
- **Shopping Cart**: Add/remove items, update quantities
- **Order Processing**: Complete checkout with form validation
- **Past Purchases**: View order history stored in browser cookies
- **Form Data Persistence**: Checkout form data saved in cookies for convenience

### 👨‍💼 Admin Features

- **Admin Authentication**: Secure login system with CAPTCHA, admin-only access, and session management
- **Product Management**: Add, edit, and delete products
- **Stock Management**: Track product inventory
- **Search Functionality**: Find products quickly

### 🎨 Design Features

- **Consistent Styling**: Unified CSS framework with CSS variables
- **Interactive Elements**: Hover effects and smooth transitions
- **Error Handling**: User-friendly error messages and notifications

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Server**: Apache (XAMPP)

## 📦 Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/download.html) (Apache, MySQL, PHP)
- Git (optional, for cloning)

### Setup Instructions

1. **Clone or Download the Project**

   ```bash
   git clone <repository-url>
   # OR download and extract the ZIP file
   ```

2. **Install XAMPP**

   - Download and install XAMPP from the official website
   - Start Apache and MySQL services from XAMPP Control Panel

3. **Setup Database**

   1. Open phpMyAdmin (http://localhost/phpmyadmin)
   2. Create a new database named `thameen`
   3. Select the new database, click **Import**
   4. Choose the file `database/thameen.sql` and click **Go**

4. **Configure Database Connection**

   - Update database credentials in `include/connection.php` if needed:

   ```php
   $conn = mysqli_connect('localhost:3307','root','','thameen');
   ```

5. **Deploy Project**
   - Copy the project folder to `C:\xampp\htdocs\`
   - Access the website at `http://localhost/Thameen/`

## 📁 Project Structure

```
Thameen/
├── 📁 adminPages/              # Admin panel pages
├── 📁 assets/                  # Static assets
├── 📁 css/                     # Global styles
├── 📁 database/                # SQL Database file
├── 📁 include/                 # Shared components
├── 📁 mainPages/              # Main website pages
├── 📁 productsPages/          # Product-related pages
└── README.md
```

## 🔧 Areas for Improvement

- [ ] Add user registration system
- [ ] Implement payment gateway integration
- [ ] Add product categories and filtering
- [ ] Add product reviews and ratings
- [ ] Implement email notifications
- [ ] Add inventory management features

## 🙏 Credits

This project is a teamwork effort and was developed as part of CIS 423: Web-based Systems Course Project (Term 2, 2023–2024). Created for educational purposes to demonstrate e-commerce system implementation.

## 📧 Support

For any questions, feel free to reach out at [F.Alkhomayes@gmail.com](mailto:F.Alkhomayes@gmail.com).

---
