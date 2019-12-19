/* eslint-disable no-console */
import axios from 'axios'
import router from '@/router/index'

console.dir(process.env)

var host = process.env.VUE_APP_API_HOST;
if (host) {
    axios.defaults.baseURL = host;
} else {
    axios.defaults.baseURL = 'http://api.edu.com';
}
axios.defaults.timeout = 5000;
axios.defaults.withCredentials = true;
// console.dir(axios.defaults)

// http request 拦截器
axios.interceptors.request.use(
    config => {
        const token = window.localStorage.getItem('token');
        config.data = JSON.stringify(config.data);
        config.headers = {
            'Content-Type': 'application/json; charset=UTF-8',
            'Accept': 'application/json',
        };
        if (token) {
            config.headers["Authorization"] = "Bearer " + token
        }
        // console.dir(axios.defaults)
        return config;
    },
    err => {
        return Promise.reject(err);
    }
);

// http response 拦截器
axios.interceptors.response.use(
    response => {
        //如果返回状态是2，跳转登录页面
        if (response.data.code == 2) {
            window.localStorage.removeItem("token");
            this.$store.dispatch('Logined', {logined:false, userName:null});
            router.push({ path: '/login', query: { redirect: router.currentRoute.fullPath } })
        }
        return response;
    },
    error => {
        console.log(error.response);
        return Promise.reject(error.response.data)
    });

    export default axios;
/**
 * fetch 请求方法
 * @param url
 * @param params
 * @returns {Promise}
 */
export function fetch(url, params = {}) {
    return new Promise((resolve, reject) => {
        axios.get(url, {
                params: params
            })
            .then(response => {
                resolve(response.data);
            })
            .catch(err => {
                reject(err)
            })
    })
}

/**
 * post 请求方法
 * @param url
 * @param data
 * @returns {Promise}
 */
export function post(url, data = {}) {
    return new Promise((resolve, reject) => {
        axios.post(url, data)
            .then(response => {
                resolve(response.data);
            }, err => {
                reject(err);
            })
    })
}

/**
 * patch 方法封装
 * @param url
 * @param data
 * @returns {Promise}
 */
export function patch(url, data = {}) {
    return new Promise((resolve, reject) => {
        axios.patch(url, data)
            .then(response => {
                resolve(response.data);
            }, err => {
                reject(err);
            })
    })
}

/**
 * put 方法封装
 * @param url
 * @param data
 * @returns {Promise}
 */
export function put(url, data = {}) {
    return new Promise((resolve, reject) => {
        axios.put(url, data)
            .then(response => {
                resolve(response.data);
            }, err => {
                reject(err);
            })
    })
}

