-- Migración: Añadir prioridad a órdenes y cierre diferido a rampas
-- Ejecutar esta consulta en tu base de datos para habilitar las nuevas funcionalidades.

ALTER TABLE ordenes ADD COLUMN prioridad_adelanto INT DEFAULT 0 AFTER id_rampa;
ALTER TABLE rampas ADD COLUMN proximo_estado ENUM('ACTIVA','INACTIVA','DESCANSO') DEFAULT NULL AFTER estado;
