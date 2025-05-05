<?php
/**
 * Plugin Name: Filtro de Productos por Vendedor para Elementor (con switch)
 * Description: Agrega la posibilidad de activar o desactivar el filtro vendor_products_only desde el panel de administración.
 * Version: 1.1
 * Author: Tu Nombre o Marca
 */

if (!defined('ABSPATH')) exit;

// Agregar opción al menú de administrador
add_action('admin_menu', function() {
    add_options_page(
        'Filtro por Vendedor',
        'Filtro por Vendedor',
        'manage_options',
        'filtro-por-vendedor',
        'filtro_vendedor_settings_page'
    );
});

// Registrar configuración
add_action('admin_init', function() {
    register_setting('filtro_vendedor_settings', 'filtro_vendedor_activo');
});

// Mostrar la página de ajustes
function filtro_vendedor_settings_page() {
    ?>
    <div class="wrap">
        <h1>Filtro de Productos por Vendedor</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('filtro_vendedor_settings');
            do_settings_sections('filtro_vendedor_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Activar filtro vendor_products_only</th>
                    <td>
                        <input type="checkbox" name="filtro_vendedor_activo" value="1" <?php checked(1, get_option('filtro_vendedor_activo', 1)); ?> />
                        <label for="filtro_vendedor_activo">Habilita o deshabilita el filtro para Elementor Loop Grid</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Mostrar notificación en admin si el filtro está desactivado
add_action('admin_notices', function() {
    if (!current_user_can('manage_options')) return;

    if (!get_option('filtro_vendedor_activo', 1)) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>Filtro por Vendedor</strong> está desactivado. Los productos de otros vendedores podrían mostrarse sin restricción.</p></div>';
    }
});

// Filtro condicional
add_action('elementor/query/vendor_products_only', function($query) {
    if (!get_option('filtro_vendedor_activo', 1)) return;

    if (!function_exists('dokan_get_store_info')) return;

    $vendor_id = get_query_var('author');

    if ($vendor_id) {
        $query->set('author', $vendor_id);
    }
});
