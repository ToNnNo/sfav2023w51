import * as bootstrap from "bootstrap";

const toastElList = document.querySelectorAll('.toast');
[...toastElList].map(toastEl => {
  console.log(toastEl);
  bootstrap.Toast.getOrCreateInstance(toastEl).show();
})


