<?php
declare(strict_types=1);
final class ProductoModelo
{
public function __construct(private PDO $pdo) {}
public function listar(string $busqueda='',int $pagina=1,int $porPagina=10): array
{
$offset=($pagina-1)*$porPagina;
$st=$this->pdo->prepare("SELECT p.id,p.nombre,p.precio,p.stock,c.nombre AS categoria FROM productos p INNER JOIN categorias c ON c.id=p.categoria_id WHERE p.activo=1 AND p.nombre LIKE :b ORDER BY p.nombre LIMIT :lim OFFSET :off");
$st->bindValue(':b','%'.$busqueda.'%');$st->bindValue(':lim',$porPagina,PDO::PARAM_INT);$st->bindValue(':off',$offset,PDO::PARAM_INT);$st->execute();
return $st->fetchAll();
}
public function crear(array $d): int
{
$st=$this->pdo->prepare("INSERT INTO productos(nombre,categoria_id,precio,stock,activo) VALUES(:nombre,:categoria,:precio,:stock,1)");
$st->execute([':nombre'=>$d['nombre'],':categoria'=>$d['categoria_id'],':precio'=>$d['precio'],':stock'=>$d['stock']]);
return (int)$this->pdo->lastInsertId();
}
public function actualizar(int $id,array $d): bool
{
$st=$this->pdo->prepare("UPDATE productos SET nombre=:nombre,categoria_id=:categoria,precio=:precio,stock=:stock WHERE id=:id");
return $st->execute([':nombre'=>$d['nombre'],':categoria'=>$d['categoria_id'],':precio'=>$d['precio'],':stock'=>$d['stock'],':id'=>$id]);
}
public function desactivar(int $id): bool
{
return $this->pdo->prepare("UPDATE productos SET activo=0 WHERE id=:id")->execute([':id'=>$id]);
}
}
