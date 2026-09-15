// Día 7: 4 cálculos con filter/map/reduce + Intl es-CO
const masCaro=productos.reduce((a,b)=>b.precio>a.precio?b:a);
const porCategoria=productos.reduce((acc,p)=>{acc[p.categoria]=(acc[p.categoria]??0)+p.stock;return acc;},{});
const stockBajo=productos.filter(p=>p.stock<5);
const promedio=productos.map(p=>p.precio).reduce((a,b)=>a+b,0)/productos.length;
const cop=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",maximumFractionDigits:0});
console.table(productos.filter(p=>p.stock>0));
console.log("Más caro:",masCaro.nombre,cop.format(masCaro.precio));
console.log("Por categoría:",porCategoria);
console.log("Stock bajo:",stockBajo.map(p=>p.nombre));
console.log("Promedio:",cop.format(promedio));
// map devuelve arreglo nuevo, forEach solo itera (devuelve undefined)
