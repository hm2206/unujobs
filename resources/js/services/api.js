import axios from 'axios';

/**
 * 
 * @param {String} method 
 * @param {String} ruta 
 * @param {Object} body 
 * @return {Promise}
 */
export const unujobs = async (method = "get", ruta, body = {}) => {    
    /**
     * Ruta para consumir api
     */
    const path = "/api/v1/";

    /**
     * Almacenamos la ruta verificada
     */
    let path_verify = "";

    /**
     * Expresión regular para quitar #slash
     */
    let validar_ruta = /^[/]/;

    /**
     * Quitar el slash de la ruta
     */
    if (validar_ruta.test(ruta)) {
        path_verify = path + ruta.slice(1, ruta.length);
    }

    /**
     * Expresión regular para saber si la ruta esta con algun origen [http://api/example] o [https://api/example]
     */
    let is_path_with_http = /^http?/;

    /**
     * Guardar ruta con origen como una ruta verificada
     */
    if (is_path_with_http.test(ruta)) {
        path_verify = ruta;
    }
    
    return await axios[method](path_verify, body);
};