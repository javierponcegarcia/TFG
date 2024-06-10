#!/bin/bash

# Actualizar el sistema
echo "Actualizando el sistema"
sudo apt-get update -y
sudo DEBIAN_FRONTEND=noninteractive apt-get upgrade -y
sudo apt install snapd -y

# INSTALACION DE MICROK8S
echo "INSTALACION DE MICROK8S"
sudo snap install microk8s --classic

# Añadir el usuario actual al grupo microk8s
echo "Añadiendo el usuario actual al grupo microk8s"
sudo usermod -aG microk8s $USER
sudo chown -f -R $USER ~/.kube


