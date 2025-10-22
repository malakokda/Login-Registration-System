# Login & Registration System

## About the Project

This is a **fully functional user authentication system** built using **HTML, CSS, PHP, JavaScript**, and **MySQL** (via XAMPP). The project allows users to **register** with secure password hashing, **log in**, and access a protected dashboard. It demonstrates core web development concepts including form handling, server-side validation, session management, and database interaction.

### 🌟 Key Features
- **User Registration** with:
  - Client-side and server-side form validation
  - Password hashing using PHP's `password_hash()`
  - Email and username uniqueness check
- **Secure User Login** with:
  - Session-based authentication
  - Protection against SQL injection
  - Redirect to dashboard upon successful login
- **Responsive Design** using CSS (mobile-friendly)
- **Interactive UI** with JavaScript (real-time feedback, password visibility toggle)
- **MySQL Database Integration** via PHP PDO (secure and modern approach)

---

### 🛠️ Tech Stack
| Technology     | Purpose |
|----------------|--------|
| **HTML5**       | Structure of web pages |
| **CSS3**        | Styling and responsive layout |
| **JavaScript**  | Client-side validation & UX enhancements |
| **PHP**         | Backend logic, form processing, session management |
| **MySQL**       | Storing user credentials securely |
| **XAMPP**       | Local development environment (Apache + MySQL) |

---

### 🚀 How It Works
1. Users access `register.php` to create an account.
2. Data is validated and stored securely in the MySQL database.
3. Users log in via `login.php` using their credentials.
4. Upon successful login, a session is created and the user is redirected to `dashboard.php`.
5. Logout functionality destroys the session.


---

### 🔒 Security Measures
- Passwords hashed with `PASSWORD_DEFAULT` (bcrypt)
- Prepared statements to prevent SQL injection
- Input sanitization and validation
- Session hijacking prevention

---

### 🎯 Future Enhancements
- [ ] Password reset via email
- [ ] Google reCAPTCHA integration
- [ ] User profile editing
- [ ] Email verification on registration
- [ ] Role-based access (admin/user)

---

### 📌 Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any PHP/MySQL environment)
- Modern browser

### 🚀 Setup Instructions
1. Start **Apache** and **MySQL** in XAMPP.
2. Clone this repo into `htdocs/your-project-folder`.
3. Import `database/users.sql` into phpMyAdmin.
4. Update `config.php` with your database credentials.
5. Access via `http://localhost/your-project-folder`

---

### 👨‍💻 Author
**Malak Okda**  
[LinkedIn] : https://www.linkedin.com/in/malak-okda/

---

> A clean, secure, and educational project perfect for learning full-stack web authentication!

---
