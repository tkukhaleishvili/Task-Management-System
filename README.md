# 🧱 Task Management System (PHP OOP MVC)

A simple Task Management System built with pure PHP (OOP style) following MVC architecture.

## 🔐 Features
- User registration and login (with CSRF + Remember Me)
- Role-based access (Admin, Manager, User)
- Task CRUD (Create, Edit, Delete)
- Soft delete (`is_deleted` + `deleted_at`)
- Task filtering and sorting
- File attachments & comments (WIP)
- Email notifications on task assignment

## 🛠️ Tech Stack
- PHP 8+
- PDO (MySQL)
- HTML + CSS
- No framework — custom MVC structure

## 🚀 Installation
```bash
git clone https://github.com/YOUR-USERNAME/task-management-system.git
cd task-management-system
composer install
