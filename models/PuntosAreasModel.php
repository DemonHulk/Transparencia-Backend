<?php

require_once 'database/conexion.php';

class PuntosAreasModel {

	/********************
		Extrae los puntos activos de un area en especifica segun su id
	********************/
    public function QueryAllPuntosAccesoAreaModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT 
									    p.id_punto,
									    p.nombre_punto,
									    COALESCE((SELECT pa1.activo 
									              FROM puntosareas pa1 
									              WHERE pa1.id_punto = p.id_punto 
									                AND pa1.id_area = :id), false) AS activo 
									FROM 
									    punto p
									ORDER BY 
									    p.orden_punto;
									");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error los puntos de acceso con el $id: " . $e->getMessage()];
        }
    }

    /********************
        Extrae las areas activas de un punto en especifico segun su id
    ********************/
    public function QueryAreaPunto_PuntoModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT 
                                        id_puntosareas, 
                                        id_punto, 
                                        id_area, 
                                        activo  
                                    from puntosareas   
                                    WHERE 
                                        id_punto = :id 
                                    ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error los puntos de acceso con el $id: " . $e->getMessage()];
        }
    }

    public function InsertPuntoAreaModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("INSERT INTO public.puntosareas (id_punto, id_area, activo, fecha_creacion, hora_creacion, fecha_actualizado) VALUES(:punto, :area, true, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
            $stmt->bindParam(':area',  $datos['area'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            return "Punto reactivado exitosamente.";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

    public function ActivatePuntoAreaModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE public.puntosareas SET  activo= true , fecha_actualizado = :fecha_actualizado WHERE id_punto = :punto and id_area = :area");
            $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
            $stmt->bindParam(':area',  $datos['area'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            return "Punto reactivado exitosamente.";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

    public function DesactivatePuntoAreaModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE public.puntosareas SET  activo= false , fecha_actualizado = :fecha_actualizado WHERE id_punto = :punto and id_area = :area");
            $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
            $stmt->bindParam(':area',  $datos['area'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            return "Punto desactivado exitosamente.";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

	public function ExistPuntoAccesoModel($datos) {
	    try {
	        $conn = Conexion::Conexion();
	        $stmt = $conn->prepare("SELECT COUNT(*) as cantidad FROM puntosareas WHERE id_punto = :punto AND id_area = :area");
	        $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
	        $stmt->bindParam(':area', $datos['area'], PDO::PARAM_INT);
	        $stmt->execute();
	        
	        $cantidad = $stmt->fetchColumn();
	        
	        if ($cantidad == 0) {
	            return false;
	        } else {
	            return true;
	        }
	    } catch (PDOException $e) {
	        return ['res' => false, 'data' => "Error al obtener todas las áreas: " . $e->getMessage()];
	    }
	}




}
