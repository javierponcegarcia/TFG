terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = "us-east-1"
}

# VPC
resource "aws_vpc" "aws_vpc_balanceo_1" {
  cidr_block = "100.100.0.0/16"
  tags = {
    Name = "Vpc balanceo principal"
  }
}

# Subredes
resource "aws_subnet" "subred_http_1" {
  vpc_id     = aws_vpc.aws_vpc_balanceo_1.id
  cidr_block = "100.100.11.0/24"
  availability_zone = "us-east-1a"
  tags = {
    Name = "Subred HTTP 1"
  }
}

resource "aws_subnet" "subred_http_2" {
  vpc_id     = aws_vpc.aws_vpc_balanceo_1.id
  cidr_block = "100.100.21.0/24"
  availability_zone = "us-east-1b"
  tags = {
    Name = "Subred HTTP 2"
  }
}

# Puerta de enlace de Internet
resource "aws_internet_gateway" "igw_1" {
  vpc_id = aws_vpc.aws_vpc_balanceo_1.id
  tags = {
    Name = "Gateway VPC principal"
  }
}

# IPs elásticas
resource "aws_eip" "elastica_1" {}

resource "aws_eip" "elastica_2" {}

# Puertas de enlace NAT
resource "aws_nat_gateway" "natgw_1" {
  allocation_id = aws_eip.elastica_1.id
  subnet_id     = aws_subnet.subred_http_1.id
  tags = {
    Name = "natgw 1"
  }
  depends_on = [aws_internet_gateway.igw_1]
}

resource "aws_nat_gateway" "natgw_2" {
  allocation_id = aws_eip.elastica_2.id
  subnet_id     = aws_subnet.subred_http_2.id
  tags = {
    Name = "natgw 2"
  }
  depends_on = [aws_internet_gateway.igw_1]
}

# Tablas de ruteo
resource "aws_route_table" "tab_ruteo_http_1" {
  vpc_id = aws_vpc.aws_vpc_balanceo_1.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.igw_1.id
  }

  tags = {
    Name = "Tabla ruteo http 1"
  }
}

resource "aws_route_table" "tab_ruteo_http_2" {
  vpc_id = aws_vpc.aws_vpc_balanceo_1.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.igw_1.id
  }

  tags = {
    Name = "Tabla ruteo http 2"
  }
}

# Asociaciones de tablas de ruteo
resource "aws_route_table_association" "a" {
  subnet_id      = aws_subnet.subred_http_1.id
  route_table_id = aws_route_table.tab_ruteo_http_1.id
}

resource "aws_route_table_association" "c" {
  subnet_id      = aws_subnet.subred_http_2.id
  route_table_id = aws_route_table.tab_ruteo_http_2.id
}

# Grupos de seguridad
resource "aws_security_group" "GS_http_1" {
  name        = "GS_http_1"
  description = "Grupo de seguridad para permitir el acceso al puerto 80, http 1"
  vpc_id      = aws_vpc.aws_vpc_balanceo_1.id

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 9000
    to_port     = 9000
    protocol    = "tcp"
    cidr_blocks = ["100.100.0.0/16"]
  }

  # Allow ICMP (ping) traffic from within the VPC
  ingress {
    from_port   = -1
    to_port     = -1
    protocol    = "icmp"
    cidr_blocks = ["100.100.0.0/16"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "Grupo seguridad http 1"
  }
}

# Par de claves
resource "aws_key_pair" "administrador" {
  key_name   = "clave_administrador"
  public_key = "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQC8puEXfi1z5zsLtViYQ8GdDkH3b328zQKmxk1oCjYHzTrrxjIH7KBdJtfIb93GPYXtLBlpkTupIl738gIOMaBmg+l00kz1sAgx8gbVsF9iWp25uDxhe839jd8KAvkN85G6JZrmsUjqdrrDc8Qd3dcel4RuNHFeKfPgG/1oSFBG+ZIfboGm05hklhlHHOfAe1dHN0wQ7BX8Ypi2mBVKqJqQUG8lw3r+3r5D4wxgUDwR9xL6zISD3e4gTLOE9bZ5IBaKjau0I8M6VOrbMoq2GA32hGXm0sU3Z9TnI2mH2d/K2aHIz0oNFdzindlSOonDOOw6aJwDR0Qv35bkjzwUbFq0Bkf8f2bGyCqns4J3ttMvYYi7u4J0tI9TMy8BzeXn+JGEVE6mOgR+D6rjdcvaSJraQ+FKyzi0Pz5UcLGD3iZK/8rsfA0m8CP5937K67qZjG228oLfR8VXh18abxv97pgq5CWx8JH15rgt9j0U3hQJaO0ZQ26soZnWXuzLenJN2p0lOeOK8IeqsSZXSF3RwW9JojukoSc2FvgyaH/VBKCzYLyV4nFZDwIcaXBKvwhIBN1yuEavzwpXZiA+88KN3cq2iw5+SVfBCxNDjbvRKfnIVj7NFvhpLzAyYqtxtcH0Zk5z302O7NF+jbivWUdnvV0vZ1mOkrb7OVru2j0HuKDqUw== jpongar@g.educaand.es"
}

# Instancias
resource "aws_instance" "HTTP_nginx_1" {
  ami           = "ami-058bd2d568351da34"
  instance_type = "t2.medium"
  key_name = "clave_administrador"
  subnet_id = aws_subnet.subred_http_1.id
  associate_public_ip_address = true
  private_ip = "100.100.11.10"
  vpc_security_group_ids = [aws_security_group.GS_http_1.id]
  tags = {
    Name = "HTTP nginx 1"
  }

  connection {
    type        = "ssh"
    user        = "admin" 
    private_key = file("C:\\Users\\Javi\\.ssh\\id_rsa")
    host        = self.public_ip
  }

  provisioner "file" {
    source      = "C:\\Users\\Javi\\Desktop\\2 ASIR\\TFG\\prueba 3\\install_kubernetes.sh"
    destination = "/home/admin/install_kubernetes.sh"
  }

  provisioner "file" {
    source      = "C:\\Users\\Javi\\Desktop\\2 ASIR\\TFG\\prueba 3\\deployment.yaml"
    destination = "/home/admin/deployment.yaml"
  }

  provisioner "remote-exec" {
    inline = [
      "sudo apt-get update -y",
      "sudo apt-get install bash -y",
      "sudo apt-get install dos2unix",
      "sudo dos2unix /home/admin/install_kubernetes.sh",
      "ls -l /home/admin/install_kubernetes.sh",
      "file /home/admin/install_kubernetes.sh",
      "sudo chmod +x /home/admin/install_kubernetes.sh",
      "bash install_kubernetes.sh",
    ]
  }

}

resource "aws_instance" "HTTP_nginx_2" {
  ami           = "ami-058bd2d568351da34"
  instance_type = "t2.medium"
  key_name = "clave_administrador"
  subnet_id = aws_subnet.subred_http_2.id
  associate_public_ip_address = true
  private_ip = "100.100.21.10"
  vpc_security_group_ids = [aws_security_group.GS_http_1.id]
  tags = {
    Name = "HTTP nginx 2"
  }

  connection {
    type        = "ssh"
    user        = "admin" 
    private_key = file("C:\\Users\\Javi\\.ssh\\id_rsa")
    host        = self.public_ip
  }

  provisioner "file" {
    source      = "C:\\Users\\Javi\\Desktop\\2 ASIR\\TFG\\prueba 3\\install_kubernetes.sh"
    destination = "/home/admin/install_kubernetes.sh"
  }
  
  provisioner "file" {
    source      = "C:\\Users\\Javi\\Desktop\\2 ASIR\\TFG\\prueba 3\\deployment.yaml"
    destination = "/home/admin/deployment.yaml"
  }

  provisioner "remote-exec" {
    inline = [
      "sudo apt-get update -y",
      "sudo apt-get install bash -y",
      "sudo apt-get install dos2unix",
      "sudo dos2unix /home/admin/install_kubernetes.sh",
      "ls -l /home/admin/install_kubernetes.sh",
      "file /home/admin/install_kubernetes.sh",
      "sudo chmod +x /home/admin/install_kubernetes.sh",
      "bash install_kubernetes.sh",
    ]
  }
}

output "ip_publica" {
  value = aws_instance.HTTP_nginx_1.public_ip
}

output "ip_publica2" {
  value = aws_instance.HTTP_nginx_2.public_ip
}


#Balanceador de carga


resource "aws_lb" "mi_balanceador_carga" {
  name               = "mi-lb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.GS_http_1.id]
  subnets            = [aws_subnet.subred_http_1.id, aws_subnet.subred_http_2.id]

  tags = {
    Name = "Mi Load Balancer"
  }
}

resource "aws_lb_target_group" "mi_target_group" {
  name     = "mi-tg"
  port     = 80
  protocol = "HTTP"
  vpc_id   = aws_vpc.aws_vpc_balanceo_1.id

  health_check {
    interval            = 30
    path                = "/"
    protocol            = "HTTP"
    timeout             = 5
    unhealthy_threshold = 2
    healthy_threshold   = 5
  }

  tags = {
    Name = "Mi Target Group"
  }
}

resource "aws_lb_listener" "mi_listener" {
  load_balancer_arn = aws_lb.mi_balanceador_carga.arn
  port              = 80
  protocol          = "HTTP"

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.mi_target_group.arn
  }
}

resource "aws_lb_target_group_attachment" "mi_attachment" {
  target_group_arn = aws_lb_target_group.mi_target_group.arn
  target_id        = aws_instance.HTTP_nginx_1.id
  port             = 80
}

resource "aws_lb_target_group_attachment" "mi_attachment_2" {
  target_group_arn = aws_lb_target_group.mi_target_group.arn
  target_id        = aws_instance.HTTP_nginx_2.id
  port             = 80
}