# centry_ps_esclavo

El módulo de Centry para prestashop te permite maneter tu catálogo de productos sincronizados entre ambas plataformas, recibiendo actualizaciones en timepo real para ver reflejados  en el ecommerce los cambios de los productos realizados en Centry y para informar a Centry de las ventas realizadas en Prestashop.

Este es un módulo que se considera esclavo de Centry, eso quiere decier que será Centry la fuente de la información de los productos y Prestashop el responsable de informar acerca de cualquier venta que ahí se genere.

## Requisitos

El único requisito para la instalación de este módulo es tener un Prestashop versión 1.7.4 o superior.

## Instalación

Este es el paso a paso para una correcta instalación del módulo en Prestashop:

1. Descarga la última versión del módulo en formato zip en la sección [releases](https://github.com/CentryCL/centry_ps_esclavo/releases/latest)
2. Dírigete a la sección "Módulos" en la administración del Prestashop.
3. Presiona el botón "Subir un módulo" ubicado en la parte superior derecha.
4. Arrastrar o seleccionar en la ventana modal el archivo zip descargado en el paso (1).
5. Dirígete a la sección "Catálogo de Módulos".
6. Busca el módulo recien cargado por el término "Centry".
7. Presiona el botón "Instalar"

## Configuración

El módulo cuanta con una sección de configuración que permite realizar algunas operaciones generales.

### Credenciales

Estas credenciales son los datos necesarios para lograr la comunicación segura entre Prestashop en Centry. Para obtenerlos se deben seguir estos pasos:

1. Ingresa a tu sesión en https://www.centry.cl
2. Dirígete a la sección [API & Apps](https://centry.cl/oauth/applications)
3. Presiona el botón "Nueva Aplicación"
4. Rellena el formulario con la siguiente información:
  * Nombre: Ingresa un nombre con el que quieras identificar tu integración. Ej: "Módulo Prestashop"
  * Redirect URI: Ingresa el valor fijo `urn:ietf:wg:oauth:2.0:oob`.
  * Scopes: Selecciona todas las casillas.
5. Guardar el formulario presionando el botón "Enviar"
6. Como resultado aparecerán los campos "App Id (o client_id)"  y "Secret" los cuales se tienen que usar para rellenar los campos del formulario de configuración del módulo.

### Campos a sincronizar



### Carga de archivo de homologación

### Descarga de archivo CSV con productos de Prestashop para Centry

## Colaboración

