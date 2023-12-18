import * as bootstrap from "bootstrap";


const toastElList = document.querySelectorAll('.toast')
const toastList = [...toastElList].map(toastEl => new bootstrap.Toast(toastEl))

console.log(toastList);
