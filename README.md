# Sistema Checador de Asistencia con Huellas Dactilares

Sistema completo de checador de asistencia basado en reconocimiento de huellas dactilares, desarrollado con PHP, MySQL y Python Flask.

## Requisitos Previos

### Software Necesario

1. **XAMPP** (o cualquier servidor Apache con PHP 7.4+)
   - PHP 7.4 o superior
   - MySQL/MariaDB
   - Apache Server

2. **Python 3.8+**
   - Con pip instalado

3. **MySQL Workbench** (o cualquier cliente MySQL)
   - Para ejecutar los scripts SQL

4. **Lector de Huellas Digital Persona U.are.U 4500**
   - Con SDK instalado

## Instalación

### Paso 1: Configurar Base de Datos

1. Abre MySQL Workbench y conéctate a tu servidor MySQL.

2. Ejecuta el script `Database/checador.sql` para crear la base de datos y las tablas:
   ```sql
   -- Ejecutar el contenido de Database/checador.sql
   ```

3. Verifica que se hayan creado las tablas:
   - `Empleados`
   - `Eventos`

### Paso 2: Configurar PHP

1. Copia la carpeta del proyecto a tu directorio de servidor web (por ejemplo, `htdocs` en XAMPP). El nombre sugerido para la carpeta es `PhpMysqlPy`.

2. Edita el archivo `connection/config.php` y ajusta las credenciales de la base de datos:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASSWORD', '');  // Tu contraseña de MySQL
   define('DB_NAME', 'Checador');
   ```

3. Asegúrate de que los directorios tengan permisos de escritura:
   - `employees_fingerprint/` (se creará automáticamente)
   - `employees_photos/` (se creará automáticamente)
   - `temp_fingerprints/` (se creará automáticamente por el API Flask)

### Paso 3: Copiar SDK de Huellas Dactilares

**IMPORTANTE:** Verifica que la carpeta `uareu/` esté presente en la raíz de `PhpMysqlPy/`. Esta carpeta ya debería estar incluida en el proyecto.

**Requisito del Cliente:** Es necesario instalar los **drivers (RTE)** del lector Digital Persona U.are.U 4500 en la computadora donde se abrirá el sistema web para que el navegador pueda comunicarse con el lector.

La estructura debe quedar así:
```
PhpMysqlPy/
├── uareu/
│   ├── lib/
│   ├── scripts/
│   ├── css/
│   └── app.js
├── public/
├── src/
└── ...
```

Si no tienes la carpeta `uareu/`, descárgala del SDK oficial de Digital Persona U.are.U 4500.

### Paso 4: Configurar API Flask (Python)

1. Abre una terminal en la carpeta `PhpMysqlPy/`.

2. Instala las dependencias de Python:
   ```bash
   pip install -r requirements.txt
   ```

3. Opcional: Configura variables de entorno (si es necesario):
   ```bash
   set DB_HOST=localhost
   set DB_USER=root
   set DB_PASSWORD=tu_password
   set DB_NAME=Checador
   ```

4. Inicia el servidor Flask:
   - **Windows (CMD):** Ejecuta `start_api_server.bat`
   - **Windows (PowerShell):** Ejecuta `start_api_server.ps1`
   - **Linux/Mac:** 
     ```bash
     cd api
     python fingerprint_matcher.py
     ```

5. Verifica que el servidor esté corriendo en `http://127.0.0.1:5000`

### Paso 5: Acceder a la Aplicación

1. Inicia Apache y MySQL desde XAMPP.

2. Abre tu navegador y accede a:
   ```
   http://localhost/PhpMysqlPy/public/index.php
   ```

## Estructura del Proyecto

```
PhpMysqlPy/
├── Database/
│   └── checador.sql          # Scripts de creación de base de datos
├── connection/
│   └── config.php            # Configuración de conexión a BD
├── api/
│   └── fingerprint_matcher.py # Servicio Flask para comparación de huellas
├── src/
│   ├── Models/               # Modelos de datos
│   ├── Repositories/         # Capa de acceso a datos (Repository Pattern)
│   └── Controllers/         # Controladores REST
├── public/
│   ├── index.php             # Página principal (Home)
│   ├── empleados/            # CRUD de empleados
│   │   ├── index.php         # Lista de empleados
│   │   ├── editar.php        # Editar empleado
│   │   └── nuevo.php         # Nuevo empleado
│   ├── reportes/
│   │   └── index.php         # Reportes y estadísticas
│   └── checador/
│       └── index.php         # Pantalla de checado automático
├── employees_fingerprint/    # Almacenamiento de huellas (se crea automáticamente)
├── employees_photos/         # Almacenamiento de fotos (se crea automáticamente)
├── uareu/                    # SDK de huellas (COPIAR DEL PROYECTO ORIGINAL)
├── requirements.txt          # Dependencias de Python
├── start_api_server.bat      # Script para iniciar API (Windows CMD)
├── start_api_server.ps1      # Script para iniciar API (PowerShell)
└── README.md                 # Este archivo
```

## Uso del Sistema

### 1. Gestión de Empleados

1. Desde la página principal, haz clic en "Empleados".
2. Para agregar un nuevo empleado:
   - Haz clic en "Agregar nuevo"
   - Completa el formulario
   - Opcionalmente captura una huella dactilar
   - Opcionalmente sube una foto
   - Guarda el empleado
3. Para editar un empleado:
   - Haz clic en el botón "Editar" junto al empleado
   - Modifica los datos necesarios
   - Guarda los cambios

### 2. Captura de Huella Dactilar

1. En la pantalla de edición/nuevo empleado, haz clic en "Capturar huella".
2. Se abrirá un modal con el lector de huellas.
3. Selecciona el lector USB conectado.
4. Haz clic en "Start" y coloca el dedo en el lector.
5. Una vez capturada, haz clic en "Guardar Huella".
6. La huella se guardará cuando guardes el empleado.

### 3. Checador Automático

1. Desde la página principal, accede a la pantalla de checador (normalmente en `checador/index.php`).
2. El sistema seleccionará automáticamente el lector USB conectado.
3. Los empleados solo necesitan colocar su dedo en el lector.
4. El sistema automáticamente:
   - Captura la huella
   - La compara con las huellas registradas
   - Si encuentra coincidencia, muestra los datos del empleado
   - Registra el evento de asistencia
   - Se resetea después de 3 segundos para el siguiente empleado

### 4. Reportes

1. Desde la página principal, haz clic en "Reportes".
2. Verás:
   - Los últimos 10 eventos registrados
   - Una gráfica con el total de usuarios que registraron eventos en la semana actual (Lunes a Sábado)

## Configuración Avanzada

### Variables de Entorno del API Flask

Puedes configurar los siguientes parámetros mediante variables de entorno:

- `DB_HOST`: Host de la base de datos (default: localhost)
- `DB_USER`: Usuario de MySQL (default: root)
- `DB_PASSWORD`: Contraseña de MySQL (default: '')
- `DB_NAME`: Nombre de la base de datos (default: Checador)
- `PORT`: Puerto del servidor Flask (default: 5000)
- `VISUAL_MATCH_THRESHOLD`: Umbral de coincidencia (default: 0.35)
- `TOP2_MARGIN`: Margen entre el mejor y segundo mejor match (default: 0.02)
- `MIN_IMAGE_QUALITY`: Calidad mínima de imagen (default: 0.30)

### Ajustar Umbrales de Comparación

Si el sistema tiene muchos falsos positivos o negativos, puedes ajustar los umbrales editando `api/fingerprint_matcher.py` o usando variables de entorno.

## Solución de Problemas

### El lector de huellas no se detecta

1. Verifica que el lector esté conectado via USB.
2. Asegúrate de que el SDK de Digital Persona esté instalado.
3. Verifica que la carpeta `uareu/` esté presente y completa.

### El API Flask no responde

1. Verifica que el servidor Flask esté corriendo en el puerto 5000.
2. Revisa los logs de Python para errores.
3. Verifica la conexión a la base de datos en `api/fingerprint_matcher.py`.

### Error de conexión a la base de datos

1. Verifica que MySQL esté corriendo.
2. Revisa las credenciales en `connection/config.php`.
3. Asegúrate de que la base de datos `Checador` exista.

### Las huellas no se comparan correctamente

1. Asegúrate de que las huellas se capturen con buena calidad.
2. Verifica que el API Flask esté accediendo correctamente a los archivos de huellas.
3. Revisa los umbrales de comparación.

## Tecnologías Utilizadas

- **Backend PHP:** PDO, Repository Pattern
- **Base de Datos:** MySQL 5.7+
- **Frontend:** Bootstrap 5, JavaScript, Chart.js, SweetAlert2
- **API:** Python Flask, OpenCV, NumPy
- **SDK:** Digital Persona U.are.U 4500 Web SDK

## Notas Importantes

- El sistema requiere que el servidor Flask esté corriendo para la funcionalidad de checador.
- Las huellas se almacenan como archivos PNG en `employees_fingerprint/`.
- Las fotos se almacenan en `employees_photos/`.
- El sistema está diseñado para uso en red local.

## Soporte

Para problemas o preguntas, revisa los logs de error de PHP y Python para más detalles.

