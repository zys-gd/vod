<?php

namespace App\Twig;

use DataDog\AuditBundle\Entity\AuditLog;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuditExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $defaults = [
            'is_safe' => ['html'],
            'needs_environment' => true,
        ];

        return [
            new TwigFunction('audit', [$this, 'audit'], $defaults),
            new TwigFunction('audit_value', [$this, 'value'], $defaults),
            new TwigFunction('audit_assoc', [$this, 'assoc'], $defaults),
            new TwigFunction('audit_blame', [$this, 'blame'], $defaults),
        ];
    }

    public function audit(Environment $twig, AuditLog $log)
    {
        $action = $log->getAction();
        return $twig->render("@Admin/Audit/{$action}.html.twig", compact('log'));
    }

    public function assoc(Environment $twig, $assoc)
    {
        return $twig->render("@Admin/Audit/assoc.html.twig", compact('assoc'));
    }

    public function blame(Environment $twig, $blame)
    {
        return $twig->render("@Admin/Audit/blame.html.twig", compact('blame'));
    }

    public function value(Environment $twig, $val)
    {
        switch (true) {
        case is_bool($val):
            return $val ? 'true' : 'false';
        case is_array($val) && isset($val['fk']):
            return $this->assoc($twig, $val);
        case is_array($val):
            return json_encode($val);
        case is_string($val):
            return strlen($val) > 60 ? substr($val, 0, 60) . '...' : $val;
        case is_null($val):
            return 'NULL';
        default:
            return $val;
        }
    }

    public function getName()
    {
        return 'app_audit_extension';
    }
}
