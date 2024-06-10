import tkinter as tk
from tkinter import messagebox
import subprocess

def construir_infraestructura():
    try:
        result = subprocess.run(['terraform', 'apply', '-auto-approve'], check=True, capture_output=True, text=True, shell=True)
        messagebox.showinfo("Éxito", "Infraestructura construida exitosamente.")
        output_text.insert(tk.END, result.stdout)
    except subprocess.CalledProcessError as e:
        messagebox.showerror("Error", f"Error al construir la infraestructura: {e}")
        output_text.insert(tk.END, e.stdout)

def destruir_infraestructura():
    try:
        result = subprocess.run(['terraform', 'destroy', '-auto-approve'], check=True, capture_output=True, text=True, shell=True)
        messagebox.showinfo("Éxito", "Infraestructura destruida exitosamente.")
        output_text.insert(tk.END, result.stdout)
    except subprocess.CalledProcessError as e:
        messagebox.showerror("Error", f"Error al destruir la infraestructura: {e}")
        output_text.insert(tk.END, e.stdout)

# Crear la ventana principal
root = tk.Tk()
root.title("Gestor de Infraestructura con Terraform")

# Crear el frame principal
frame = tk.Frame(root)
frame.pack(pady=20, padx=20)

# Botón para construir la infraestructura
btn_construir = tk.Button(frame, text="Construir Infraestructura", command=construir_infraestructura, width=30)
btn_construir.grid(row=0, column=0, pady=10)

# Botón para destruir la infraestructura
btn_destruir = tk.Button(frame, text="Destruir Infraestructura", command=destruir_infraestructura, width=30)
btn_destruir.grid(row=1, column=0, pady=10)

# Caja de texto para la salida
output_text = tk.Text(frame, height=10, width=80)
output_text.grid(row=2, column=0, pady=10)

# Ejecutar el bucle principal de la ventana
root.mainloop()