# Rapaeng Cafe - POS with Inventory Management System

A comprehensive Point of Sale (POS) and Inventory Management System built for Rapaeng Cafe, designed to streamline operations, manage inventory, and process transactions efficiently.

## 📋 Features

- **Point of Sale (POS) Interface** - User-friendly cashier interface for processing orders and transactions
- **Inventory Management** - Track stock levels, manage products, and receive low-stock alerts
- **Admin Dashboard** - Comprehensive administrative controls and analytics
- **Sales Reports** - Generate detailed sales reports and analytics
- **User Management** - Role-based access control for staff and administrators
- **Product Catalog** - Manage menu items, categories, and pricing

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL/MariaDB
- **Additional:** AJAX for asynchronous operations

## 📁 Project Structure

```
Rapaeng-Cafe-POS/
├── api/              # API endpoints for backend operations
├── css/              # Stylesheets
├── images/           # Image assets
├── js/               # JavaScript files
├── pages/            # Application pages
├── sql/              # Database schema and migrations
├── index.php         # Main entry point
└── README.md         # Project documentation
```

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Web server (Apache/Nginx)
- Web browser (Chrome, Firefox, Edge, Safari)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/shvsha/Rapaeng-Cafe---POS-with-Inventory-Management-System.git
   cd Rapaeng-Cafe---POS-with-Inventory-Management-System
   ```

2. **Set up the database**
   - Create a new MySQL database
   - Import the SQL file from the `sql/` directory
   ```sql
   CREATE DATABASE pos-inve_db;
   USE pos-inve_db;
   SOURCE sql/database.sql;
   ```

3. **Configure database connection**
   - Update the database configuration in your connection file
   - Set your database credentials (host, username, password, database name)

4. **Deploy to web server**
   - Place the project files in your web server's document root
   - For XAMPP: `htdocs/`
   - For WAMP: `www/`
   - For production: Configure your Apache/Nginx virtual host

5. **Access the application**
   - Open your browser and navigate to:
   ```
   http://localhost/Rapaeng-Cafe---POS-with-Inventory-Management-System/
   ```

### Default Login Credentials

```
Admin:
Username: ainahatdog
Password: 1234567

Cashier:
Username: saleiri
Password: monje123

Customer:
Username: shasha
Password: ivannigos
```
*⚠️ Change these credentials immediately after first login for security purposes*

## 💡 Usage

### For Cashiers
1. Log in with cashier credentials
2. Select items from the product catalog
3. Add items to the cart
4. Process payment and complete transaction

### For Administrators
1. Log in with admin credentials
2. Access the admin dashboard
3. Manage inventory, users, and products
4. View sales reports and analytics

## 🔒 Security Features

- Password hashing for user authentication
- SQL injection prevention
- XSS protection
- Session management
- Role-based access control

## 📊 Database Schema

The system uses a relational database with the following main tables:
- `customers` - User accounts and authentication
- `menu`- Product catalog and inventory
- `category` - Product categories
- `orders` - Sales transactions
- `stock-orders` - Inventory movement history

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👤 Author

**shvsha**
- GitHub: [@shvsha](https://github.com/shvsha)

## 📧 Support

For support, please open an issue in the GitHub repository or contact the development team.

## 🔄 Changelog

### Version 1.0.0 (Latest)
- Initial release
- Basic POS functionality
- Inventory management system
- Admin dashboard
- Sales reporting
- User authentication

## 🎯 Future Enhancements

- [ ] Mobile responsive design
- [ ] Receipt printer integration
- [ ] Barcode scanner support
- [ ] Customer loyalty program
- [ ] Multi-branch support
- [ ] Advanced analytics dashboard
- [ ] Email notifications
- [ ] Backup and restore functionality

---

Made with ❤️ for Rapaeng Cafe
