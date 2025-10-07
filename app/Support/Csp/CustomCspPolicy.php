<?php

namespace App\Support\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Policy as BasePolicy;

class CustomCspPolicy extends BasePolicy
{
    public function configure()
    {
        $this
            // Default source - fallback for other directives
            ->addDirective(Directive::DEFAULT, "'self'")
            
            // Scripts
            ->addDirective(Directive::SCRIPT, "'self'")
            ->addDirective(Directive::SCRIPT, "'unsafe-inline'") // Required for Bootstrap and inline scripts
            ->addDirective(Directive::SCRIPT, 'cdn.jsdelivr.net')
            ->addDirective(Directive::SCRIPT, 'cdnjs.cloudflare.com')
            
            // Styles
            ->addDirective(Directive::STYLE, "'self'")
            ->addDirective(Directive::STYLE, "'unsafe-inline'") // Required for Bootstrap and inline styles
            ->addDirective(Directive::STYLE, 'cdn.jsdelivr.net')
            ->addDirective(Directive::STYLE, 'cdnjs.cloudflare.com')
            ->addDirective(Directive::STYLE, 'fonts.googleapis.com')
            
            // Images
            ->addDirective(Directive::IMG, "'self'")
            ->addDirective(Directive::IMG, 'data:')
            ->addDirective(Directive::IMG, 'https:')
            
            // Fonts
            ->addDirective(Directive::FONT, "'self'")
            ->addDirective(Directive::FONT, 'data:')
            ->addDirective(Directive::FONT, 'fonts.gstatic.com')
            ->addDirective(Directive::FONT, 'cdnjs.cloudflare.com')
            
            // Connect (AJAX, WebSocket, EventSource)
            ->addDirective(Directive::CONNECT, "'self'")
            
            // Media (audio, video)
            ->addDirective(Directive::MEDIA, "'self'")
            
            // Objects (Flash, Java, etc.)
            ->addDirective(Directive::OBJECT, "'none'")
            
            // Frames
            ->addDirective(Directive::FRAME, "'self'")
            
            // Base URI
            ->addDirective(Directive::BASE, "'self'")
            
            // Form actions
            ->addDirective(Directive::FORM_ACTION, "'self'")
            
            // Frame ancestors (clickjacking protection)
            ->addDirective(Directive::FRAME_ANCESTORS, "'self'")
            
            // Upgrade insecure requests (HTTP to HTTPS)
            ->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, '');
    }
}
