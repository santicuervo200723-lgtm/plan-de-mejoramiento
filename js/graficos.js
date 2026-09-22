const PALETA=["#39A900","#15506B","#F08A00","#8FD46A","#B0209E","#00A0C6"];
const moneda=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",maximumFractionDigits:0});
let gV,gC,gL;
async function dibujarGraficos(){
try{
const d=await fetch("api/graficos.php",{credentials:"same-origin"}).then(r=>r.json());
if(window.Chart){
gV?.destroy();
gV=new Chart(document.getElementById("g-ventas"),{type:"bar",data:{labels:d.ventasMes.etiquetas,datasets:[{label:"Ventas",data:d.ventasMes.valores,backgroundColor:PALETA[0],borderRadius:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>moneda.format(c.parsed.y)}}},scales:{y:{beginAtZero:true,ticks:{callback:v=>(v/1000000)+" M"}}}}});
gC?.destroy();
gC=new Chart(document.getElementById("g-categorias"),{type:"doughnut",data:{labels:d.categorias.etiquetas,datasets:[{data:d.categorias.valores,backgroundColor:PALETA}]},options:{responsive:true,maintainAspectRatio:false,cutout:"62%",plugins:{legend:{position:"right"}}}});
if(document.getElementById("g-linea")){
gL?.destroy();
gL=new Chart(document.getElementById("g-linea"),{type:"line",data:{labels:d.categorias.etiquetas,datasets:[{label:"Total",data:d.categorias.valores,borderColor:PALETA[1],fill:false}]},options:{responsive:true,maintainAspectRatio:false}});
}
}}catch(e){console.log("Sin datos API aún",e);}}
document.addEventListener("DOMContentLoaded",dibujarGraficos);
