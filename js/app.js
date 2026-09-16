// Día 8: menú + delegación + validación (sin onclick en HTML)
document.querySelector(".boton-menu")?.addEventListener("click",()=>{
const m=document.querySelector(".panel__menu");m.classList.toggle("abierto");
document.querySelector(".boton-menu").setAttribute("aria-expanded",m.classList.contains("abierto"));
});
const form=document.querySelector("#form-producto");
form?.addEventListener("submit",e=>{
const precio=form.elements.precio;
precio.setCustomValidity(Number(precio.value)<=0?"El precio debe ser mayor que cero.":"");
if(!form.checkValidity()){e.preventDefault();form.reportValidity();}
});
