<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * RF-06: "salida" incluye la contraseña temporal enviada por email. En local
 * MAIL_MAILER=log la manda a storage/logs/laravel.log en vez de un correo
 * real (mismo mecanismo que RF-03).
 */
class CuentaCreada extends Notification
{
    use Queueable;

    public function __construct(private readonly string $passwordTemporal)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu cuenta en Compass')
            ->greeting("Hola {$notifiable->nombre_completo},")
            ->line('Un administrador creo una cuenta para ti en Compass.')
            ->line("Correo: {$notifiable->email}")
            ->line("Contraseña temporal: {$this->passwordTemporal}")
            ->line('Te recomendamos cambiarla apenas inicies sesion, usando la opcion "¿Olvidaste tu contraseña?".')
            ->action('Iniciar sesion', route('login'));
    }
}
