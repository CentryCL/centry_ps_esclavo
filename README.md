# Módulo Centry para Prestahsop

El módulo de Centry para PrestaShop te permite maneter tu catálogo de productos
sincronizados entre ambas plataformas, recibiendo actualizaciones en tiempo real
para ver reflejados en el e-commerce los cambios de los productos realizados en
Centry y para informar a Centry de las ventas realizadas en PrestaShop.

Este es un módulo que se considera esclavo de Centry, eso quiere decir que será
Centry la fuente de la información de los productos y PrestaShop el responsable
de informar acerca de cualquier venta que ahí se genere.

## Requisitos

El único requisito para la instalación de este módulo es tener un PrestaShop
versión 1.7.6 o superior.

## Instalación

Este es el paso a paso para una correcta instalación del módulo en PrestaShop:

1. Descarga la última versión del módulo en formato zip en la sección [releases](https://github.com/CentryCL/centry_ps_esclavo/releases/latest)
2. Dirígete a la sección "Módulos" en la administración del PrestaShop.
3. Presiona el botón "Subir un módulo" ubicado en la parte superior derecha.
4. Arrastrar o seleccionar en la ventana modal el archivo zip descargado en el paso (1).
5. Dirígete a la sección "Catálogo de Módulos".
6. Busca el módulo recién cargado por el término "Centry".
7. Presiona el botón "Instalar"

## Configuración

El módulo cuanta con una sección de configuración que permite realizar algunas
operaciones generales.

### Credenciales

Estas credenciales son los datos necesarios para lograr la comunicación segura
entre PrestaShop en Centry. Para obtenerlos se deben seguir estos pasos:

1. Ingresa a tu sesión en https://www.centry.cl
2. Dirígete a la sección [API & Apps](https://centry.cl/oauth/applications)
3. Presiona el botón "Nueva Aplicación"
4. Rellena el formulario con la siguiente información:
   - Nombre: Ingresa un nombre con el que quieras identificar tu integración. Ej: "Módulo PrestaShop"
   - Redirect URI: Ingresa el valor fijo `urn:ietf:wg:oauth:2.0:oob`.
   - Scopes: Selecciona todas las casillas.
5. Guardar el formulario presionando el botón "Enviar"
6. Como resultado aparecerán los campos "App Id (o client_id)" y "Secret" los cuales se tienen que usar para rellenar los campos del formulario de configuración del módulo.

### Campos a sincronizar

El módulo ofrece controlar en qué momento de la vida de un producto se tomará la
información proveniente de Centry para cargarla en PrestaShop. Básicamente estos
momentos pueden ser en la creación de la publicación en PrestaShop o en las
posteriores actualizaciones que sufra el producto en Centry.

Los campos disponibles a sincronizar son:

- Nombre
- Precio
- Precio de oferta
- Descripción
- Sku del Producto
- Características
- Stock
- Sku de la Variante
- Talla
- Color
- Código de barras
- Imágenes Producto
- Condición
- Garantía
- Estado
- Campos SEO
- Marca
- Medidas del paquete
- Categoría

**IMORTANTE**

El código de barras es especialmente delicado porque PrestaShop valida que su
valor cumpla con los estándares EAN, UPC, etc. y si el valor que viene desde
Centry no es válido entonces tanto la creación como la actualización fallará. La
recomendación es que si no se tiene seguridad sobre la correctitud de estos
datos, entonces es mejor mantener desseleccionados estos campos y completar
directamente en PrestaShop estos valores.

### Homologación de estados de pedidos

Como el módulo informa a Centry los pedidos que en PrestaShop se generen, es
necesario traducir los distintos estados de pedidos que PrestaShop maneja a uno
específico de Centry. En esta sección se listan todos los estados registrados en
PrestaShop y se ofrece seleccionar uno de los posibles estados de Centry. Por lo
general los PrestaShop manejan un número cercano a 10 estado distintos mientras
que en Centry sólo se disponen de 4, no existe ningún problema si dos estados
distintos están homologados con la misma opción de Centry. Tampoco es necesario
que todas las opciones sean usadas una vez, aunque sí es muy recomendado.

### Descarga de archivo CSV con productos de PrestaShop para Centry

## Colaboración
