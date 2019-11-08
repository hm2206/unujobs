import axios from 'axios';

let path = "/api/v1/"; 

/**
 * 
 * @param {String} method 
 * @param {String} ruta 
 * @param {Object} body 
 * @return {Promise}
 */
export const unujobs = async (method = "get", ruta, body = {}) => {    
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


export const URI = path;


export const printReport = (url, name = "Reporte", config = ["width=1024", "height=700"]) => {
    return window.open(`${URI}${url}`, name, config.join(','));
};