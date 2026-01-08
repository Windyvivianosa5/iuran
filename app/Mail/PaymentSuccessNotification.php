<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;

class PaymentSuccessNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->user = $transaction->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Iuran PGRI Berhasil - ' . $this->transaction->order_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'transaction' => $this->transaction,
                'user' => $this->user,
                'settlementTime' => $this->transaction->settlement_time ?? $this->transaction->updated_at,
                'detailUrl' => url('/kabupaten/dashboard/iuran/' . $this->transaction->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
