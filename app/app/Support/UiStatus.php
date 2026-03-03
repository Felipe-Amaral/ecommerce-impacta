<?php

namespace App\Support;

use BackedEnum;
use UnitEnum;

final class UiStatus
{
    public static function label(mixed $value): string
    {
        if (is_object($value) && method_exists($value, 'label')) {
            /** @var object{label: callable():string} $value */
            return $value->label();
        }

        $raw = self::raw($value);
        if ($raw === null || $raw === '') {
            return '-';
        }

        return self::labelValue($raw);
    }

    public static function icon(mixed $value): string
    {
        if (is_object($value) && method_exists($value, 'icon')) {
            /** @var object{icon: callable():string} $value */
            return $value->icon();
        }

        $raw = self::raw($value);
        if ($raw === null || $raw === '') {
            return 'status-generic';
        }

        return self::iconValue($raw);
    }

    public static function tone(mixed $value): string
    {
        if (is_object($value) && method_exists($value, 'tone')) {
            /** @var object{tone: callable():string} $value */
            return $value->tone();
        }

        $raw = self::raw($value);
        if ($raw === null || $raw === '') {
            return 'neutral';
        }

        return self::toneValue($raw);
    }

    public static function labelValue(string $value): string
    {
        $key = self::normalize($value);

        return self::labels()[$key] ?? self::humanize($value);
    }

    public static function iconValue(string $value): string
    {
        $key = self::normalize($value);

        return self::icons()[$key] ?? 'status-generic';
    }

    public static function toneValue(string $value): string
    {
        $key = self::normalize($value);

        return self::tones()[$key] ?? 'neutral';
    }

    public static function humanize(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '-';
        }

        $text = str_replace(['_', '-'], ' ', $value);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $lower = mb_strtolower($text, 'UTF-8');

        return mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
    }

    private static function raw(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof UnitEnum) {
            return (string) $value->name;
        }

        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        return null;
    }

    private static function normalize(string $value): string
    {
        return strtolower(trim($value));
    }

    /**
     * @return array<string, string>
     */
    private static function labels(): array
    {
        return [
            'draft' => 'Rascunho',
            'pending' => 'Pendente',
            'pending_payment' => 'Aguardando pagamento',
            'paid' => 'Pago',
            'authorized' => 'Autorizado',
            'in_production' => 'Em produção',
            'production' => 'Produção',
            'prepress' => 'Pré-impressão',
            'approved' => 'Aprovado',
            'printing' => 'Impressão',
            'finishing' => 'Acabamento',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'canceled' => 'Cancelado',
            'cancelled' => 'Cancelado',
            'failed' => 'Falhou',
            'rejected' => 'Rejeitado',
            'refunded' => 'Reembolsado',
            'partially_refunded' => 'Reembolso parcial',
            'uploaded' => 'Enviado',
            'under_review' => 'Em revisão',
            'needs_adjustment' => 'Ajuste solicitado',
            'pending_file' => 'Aguardando arte',
            'file_sent' => 'Arquivo enviado',
            'file_under_review' => 'Arquivo em revisão',
            'file_approved' => 'Arquivo aprovado',
            'file_adjustment_requested' => 'Ajuste de arte solicitado',
            'pix' => 'PIX',
            'credit_card' => 'Cartão de crédito',
            'boleto' => 'Boleto',
            'bank_transfer' => 'Transferência bancária',
            'manual' => 'Manual',
            'nao informado' => 'Não informado',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function icons(): array
    {
        return [
            'draft' => 'status-draft',
            'pending' => 'status-pending',
            'pending_payment' => 'status-pending',
            'authorized' => 'status-authorized',
            'paid' => 'status-paid',
            'in_production' => 'status-production',
            'production' => 'status-production',
            'prepress' => 'status-prepress',
            'approved' => 'status-approved',
            'printing' => 'status-printing',
            'finishing' => 'status-finishing',
            'shipped' => 'status-shipped',
            'delivered' => 'status-delivered',
            'canceled' => 'status-canceled',
            'cancelled' => 'status-canceled',
            'failed' => 'status-failed',
            'rejected' => 'status-failed',
            'refunded' => 'status-refunded',
            'partially_refunded' => 'status-refunded',
            'uploaded' => 'status-uploaded',
            'under_review' => 'status-review',
            'needs_adjustment' => 'status-adjustment',
            'pending_file' => 'status-pending',
            'file_sent' => 'status-uploaded',
            'file_under_review' => 'status-review',
            'file_approved' => 'status-approved',
            'file_adjustment_requested' => 'status-adjustment',
            'pix' => 'status-pix',
            'credit_card' => 'status-card',
            'boleto' => 'status-billing',
            'bank_transfer' => 'status-bank',
            'manual' => 'status-generic',
            'nao informado' => 'status-generic',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function tones(): array
    {
        return [
            'draft' => 'neutral',
            'pending' => 'warning',
            'pending_payment' => 'warning',
            'authorized' => 'info',
            'paid' => 'success',
            'in_production' => 'info',
            'production' => 'info',
            'prepress' => 'info',
            'approved' => 'success',
            'printing' => 'info',
            'finishing' => 'info',
            'shipped' => 'accent',
            'delivered' => 'success',
            'canceled' => 'danger',
            'cancelled' => 'danger',
            'failed' => 'danger',
            'rejected' => 'danger',
            'refunded' => 'neutral',
            'partially_refunded' => 'neutral',
            'uploaded' => 'accent',
            'under_review' => 'info',
            'needs_adjustment' => 'danger',
            'pending_file' => 'warning',
            'file_sent' => 'accent',
            'file_under_review' => 'info',
            'file_approved' => 'success',
            'file_adjustment_requested' => 'danger',
            'pix' => 'success',
            'credit_card' => 'accent',
            'boleto' => 'warning',
            'bank_transfer' => 'info',
            'manual' => 'neutral',
            'nao informado' => 'neutral',
        ];
    }
}
