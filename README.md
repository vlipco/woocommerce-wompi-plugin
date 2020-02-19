# Plugin WooCommerce para Wompi
Este repositorio implementa el plugin de WooCommerce (solución eCommerce de WordPress) para la pasarela de pagos **[Wompi](https://wompi.co)**.


## Requisitos
1. Contar con un website que funcione con WordPress y tenga WooCommerce instalado
1. Tener permisos en el administrador de WordPress para instalar nuevos plugins
1. [Crear una cuenta Wompi](https://comercios.wompi.co) para contar un par de llaves (pública y privada), tanto para el ambiente de _Producción_ como _Sandbox_
1. Haber completado el proceso de vinculación. que permite procesar pagos con Wompi


## Funcionalidades
Este plugin:
1. Permite usar el botón de pagos de Wompi y, por lo tanto, aceptar pagos con cualquiera de los métodos de pago disponibles para un comercio.
1. Permite ser usado para aceptar pagos reales (en _Producción_), así como pagos en modo de pruebas (_Sandbox_)
1. Incluye traducciones para instalaciones de WordPress en 3 idiomas: español, inglés y portugués


## Uso
Luego de instalar el plugin, dirígete a la configuración del mismo y llena los campos para cada par de llaves (pública y privada), en cada ambiente. Cada par de llaves lo encuentras en la sección **[Mi cuenta](https://comercios.wompi.co/my-account)** de tu cuenta del dashboard de comercios.

Cada par de llaves (pública y privada) son similares a las mostradas a continuación. Estas llaves son **solo un ejemplo**; usa tus propias llaves al configurar el plugin.
- Ejemplo de llave pública de pruebas: `pub_test_a84dCman2sDd983nsZakwe9alpormn21`
- Ejemplo de llave privada de pruebas: `prv_test_iqFHLVcpPNwecpUasJhGAZsC4sWU59TL`
- Ejemplo de llave pública de producción: `pub_prod_NaWQp9UH3CjSt8gFSCpM5PmHLDkiVFf7`
- Ejemplo de llave privada de producción: `prv_prod_ZB94WZJEirKDDYicSDSew56MsCIG5ZXz`

Una vez hecho esto, **marca o desmarca** la opción de `Habilitar modo de prueba` para decidir si quieres hacer transacciones de prueba (_Sandbox_), o con dinero real (_Producción_), respectivamente.


## Licencia
Este plugin se ofrece y distribuye bajo la [licencia LGPL 3.0](LICENSE)
