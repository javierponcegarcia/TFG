#!/bin/bash

# Habilitar complementos necesarios
echo "Habilitando complementos necesarios de MicroK8s"
microk8s enable dns dashboard

# Configurar alias para kubectl
echo "Configurando alias para kubectl"
sudo snap alias microk8s.kubectl kubectl

# Desplegar la aplicación web
echo "Desplegando la aplicación web"
microk8s kubectl apply -f deployment.yaml
microk8s kubectl apply -f db-deployment.yaml
microk8s kubectl apply -f db-configmap.yaml

# Mostrar información de los pods y servicios
microk8s kubectl get pods
microk8s kubectl get services