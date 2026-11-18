# Almacen - 2026-ii

Proyecto de ejemplo para el curso Desarrollo de Software I (UAC, Filial Quillabamba).

## Requisitos

Verifica que tengas todo instalado antes de empezar:

| Herramienta | Versión | Comando de verificación |
|---|---|---|
| PHP | 8.4 (obligatorio) | `php -v` |
| Composer | 2.x | `composer -V` |
| MySQL o MariaDB | 8.x / 10.x | `mysql --version` |
| Node.js y npm | 20 o superior | `node -v` y `npm -v` |
| Git | cualquiera | `git --version` |

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/RedyDelgado/almacen.git
cd example-ap
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Crear el archivo de entorno

Windows (CMD):

```cmd
copy .env.example .env
```

### 4. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5. Crear la base de datos

Entra a MySQL y crea la base de datos vacía:

```sql
CREATE DATABASE db_almacen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Configurar el archivo `.env`

Edita estas líneas con tus datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_almacen
DB_USERNAME=root
DB_PASSWORD=
```

El nombre de `DB_DATABASE` debe ser igual al de la base creada en el paso 5.

### 7. Ejecutar migraciones y datos iniciales

```bash
php artisan migrate --seed
```
### 8. Instalar Vite

```bash
npm install tailwindcss @tailwindcss/vite
```

```bash
npm run dev
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

Abre http://127.0.0.1:8000 en el navegador.

## Errores frecuentes

| Error | Causa | Solución |
|---|---|---|
| `Your PHP version does not satisfy that requirement` | PHP distinto de 8.4 | Instalar PHP 8.4 y verificar con `php -v` |
| `could not find driver` | Falta `pdo_mysql` | Activar la extensión en `php.ini` o instalar `php8.4-mysql` |
| `No application encryption key has been specified` | No se generó la clave | Ejecutar `php artisan key:generate` |
| `SQLSTATE[HY000] [1049] Unknown database` | La base de datos no existe | Crearla (paso 5) y revisar `DB_DATABASE` |
| `SQLSTATE[HY000] [1045] Access denied` | Usuario o contraseña incorrectos | Corregir `DB_USERNAME` y `DB_PASSWORD` en `.env` |
| `Vite manifest not found` | Frontend sin compilar | Ejecutar `npm install` y `npm run build` |
| `Class not found` o cambios que no se reflejan | Caché desactualizada | `composer dump-autoload` y `php artisan optimize:clear` |
| Error de permisos en `storage` o `bootstrap/cache` (Linux) | Permisos de escritura | `chmod -R 775 storage bootstrap/cache` |

## Actualizar el proyecto

Si el docente publica cambios:

```bash
git pull
composer install
php artisan migrate
npm install
npm run build
php artisan optimize:clear
```

## Filtro propio: nivel de stock

En el listado de productos agregué el filtro **Nivel de stock**, que clasifica los productos en tres estados: **Agotado** (stock igual a 0), **Stock bajo** (de 1 a 10 unidades) y **Stock normal** (más de 10 unidades). Lo diseñé pensando en el almacenero: en el día a día lo que más necesita saber es qué productos hay que reponer, y buscarlos uno por uno entre miles de filas no es práctico. Con este filtro puede ver al instante qué se agotó o qué está por agotarse. Funciona junto con los demás filtros (búsqueda, categoría y almacén): si se elige un almacén, el nivel se calcula con el stock de ese almacén; si no, se muestran los productos que tengan ese nivel en algún almacén. El filtro se aplica en SQL con `where()` y `whereHas()`, por lo que la base de datos solo devuelve las 10 filas de la página actual, y queda guardado en la URL con `#[Url]` (por ejemplo `/productos?nivel=bajo&categoria_id=3`), de modo que sobrevive al F5 y se puede compartir el enlace.

## Formularios con validación (Tarea 03)

Los formularios de **productos** (`/productos`) y de **proveedores** (`/proveedores`) son componentes Livewire que validan en tiempo real (`wire:model.blur` + `validateOnly`) y con mensajes en español. Las mismas reglas están copiadas en `StoreProductoRequest` y `StoreProveedorRequest`, de modo que el API (`/api/productos`, `/api/proveedores`) rechaza lo mismo que el formulario.

| Formulario | Dato inválido | Regla que lo impide |
|---|---|---|
| Producto | Precio `-5` (o `0`) | `gt:0` |
| Producto | Nombre de 101 letras (la columna mide 100) | `max:100` |
| Producto | Código `HER-0001` que ya existe en otro producto | `Rule::unique('productos', 'codigo')->ignore($this->producto?->id)` |
| Proveedor | RUC de 10 dígitos o con letras | `digits:11` |
| Proveedor | Correo `sin-arroba` | `email` |
| Proveedor | RUC o correo ya registrados en otro proveedor | `Rule::unique('proveedores', 'ruc')` y `Rule::unique('proveedores', 'email')` con `ignore()` |
