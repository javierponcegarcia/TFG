#!/bin/bash

# INSTALACION DE MINIKUBE, DOCKER Y KUBECTL

# INSTALACION MINIKUBE
echo "INSTALACION DE MINIKUBE"
sudo apt update -y
sudo apt install curl -y
curl -LO https://storage.googleapis.com/minikube/releases/latest/minikube_latest_amd64.deb
sudo dpkg -i minikube_latest_amd64.deb

# INSTALACION DOCKER
echo "INSTALACION DE DOCKER"
sudo apt install software-properties-common apt-transport-https ca-certificates curl gnupg lsb-release -y
mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update -y
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-compose-plugin -y
sudo systemctl enable docker

# INICIAR MINIKUBE
echo "INICIO DE MINIKUBE"
sudo usermod -aG docker $USER
newgrp docker <<EOF
minikube start

# INSTALACION KUBECTL
echo "INSTALACION DE KUBECTL"
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
sudo install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl
kubectl version --client
kubectl get po -A
EOF

#Instalacion DEployment
echo "Instalacion DEployment APP WEB"
kubectl apply -f deployment.yaml

