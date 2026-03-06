# dualab-alumnos
Aplicación para la bienvenida de alumnos de nuevo ingreso


BACKEND:
composer create-project laravel/laravel nombre-proyecto
php artisan serve

Crear un modelo:
bashphp artisan make:model Producto
Esto genera app/Models/Producto.php

-----------------------------------------------------

FRONTEND:
<!-- npm install vue@next -->
<!-- npm install vue@3 @vitejs/plugin-vue vue-router axios -->
npm create vite@latest frontend -- --template vue
cd frontend
npm install
npm install axios
npm install -D tailwindcss @tailwindcss/vite
npm run dev