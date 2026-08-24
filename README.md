# CakePHP Clone (Ultra-Low Inode MVC)

A lightweight CakePHP 4/5 style MVC CRUD application crafted specifically for shared/free hosting environments (e.g. **InfinityFree**, **Aeon**, cPanel) where strict inode (file count) limits exist.

## 📊 Inode Stats
- **Total Files:** ~17 files
- **Total Inodes:** < 20
- **Standard CakePHP Inodes:** 4,000 – 6,000+ files
- **Savings:** **99.5% fewer inodes**

## 🚀 Directory Structure
```text
cakephp-clone/
├── cake_core/
│   └── Cake.php               (Table/Entity ORM, Router, Request, Flash, Configure)
├── config/
│   ├── app.php                (Database & App Settings)
│   └── routes.php             (Route definitions)
├── src/
│   ├── Controller/
│   │   ├── AppController.php  (Base controller with Flash & Layout rendering)
│   │   └── ProductsController.php (CRUD actions: index, view, add, edit, delete)
│   ├── Model/
│   │   ├── Entity/
│   │   │   └── Product.php    (Product Entity class)
│   │   └── Table/
│   │       └── ProductsTable.php (Products Table / Model)
│   └── View/
│       ├── layout/
│       │   └── default.php    (Main layout wrapper)
│       └── Products/
│           ├── index.php
│           ├── add.php
│           ├── edit.php
│           └── view.php
├── webroot/
│   ├── index.php              (Web entry point)
│   └── .htaccess
├── index.php                  (Root fallback)
└── .htaccess
```

## 🛠 Features & Conventions
- **Conventions:** Follows CakePHP naming and architecture (`ProductsController`, `ProductsTable`, `Product` entity, `src/View/Products/add.php`).
- **ORM:** Supports `$this->Products->find('all')`, `$this->Products->get($id)`, `$this->Products->newEmptyEntity()`, `$this->Products->patchEntity()`, `$this->Products->save()`, `$this->Products->delete()`.
- **Layout & Flash:** Automatically wraps views inside `layout/default.php` with `$this->Flash->render()`.
- **Database:** Auto-configured **SQLite** (0-setup) with instant toggle for **MySQL** in `config/app.php`.

## 💻 Local Testing
Run with PHP built-in server:
```bash
cd cakephp-clone
php -S localhost:8000
```
Open `http://localhost:8000` in your browser.
