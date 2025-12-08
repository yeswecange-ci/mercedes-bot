# Script PowerShell pour mettre à jour le flow Twilio avec la vérification client existant
# Auteur: Assistant IA
# Date: 2025-12-08

Write-Host "🔄 Mise à jour du flow Twilio..." -ForegroundColor Cyan

# Charger le flow original
$flowPath = "c:\YESWECANGE\Mercedes-bot App\twilio-flow-complete-integrated.json"
$outputPath = "c:\YESWECANGE\Mercedes-bot App\twilio-flow-optimized-v3.2.json"

if (-not (Test-Path $flowPath)) {
    Write-Host "❌ Fichier source introuvable: $flowPath" -ForegroundColor Red
    exit 1
}

Write-Host "📖 Lecture du flow original..." -ForegroundColor Yellow
$flow = Get-Content $flowPath -Raw | ConvertFrom-Json

# Mise à jour de la description
$flow.description = "Mercedes-Benz by CFAO - WhatsApp Bot v3.2 - Optimisé avec vérification client existant"

Write-Host "🔍 Recherche du widget api_incoming..." -ForegroundColor Yellow

# Trouver et mettre à jour le widget api_incoming
foreach ($state in $flow.states) {
    if ($state.name -eq "api_incoming") {
        Write-Host "✅ Widget api_incoming trouvé, mise à jour du body..." -ForegroundColor Green
        
        # Mettre à jour le body pour inclure les médias
        $newBody = @{
            "From" = "{{trigger.message.From}}"
            "Body" = "{{trigger.message.Body}}"
            "MessageSid" = "{{trigger.message.MessageSid}}"
            "ProfileName" = "{{trigger.message.ProfileName}}"
            "NumMedia" = "{{trigger.message.NumMedia}}"
            "MediaUrl0" = "{{trigger.message.MediaUrl0}}"
            "MediaContentType0" = "{{trigger.message.MediaContentType0}}"
        }
        
        $state.properties.body = ($newBody | ConvertTo-Json -Compress).Replace('"{{', '{{').Replace('}}"', '}}')
    }
    
    # Remplacer check_existing_name par check_client_exists
    if ($state.name -eq "check_existing_name") {
        Write-Host "✅ Remplacement de check_existing_name par check_client_exists..." -ForegroundColor Green
        
        $state.name = "check_client_exists"
        $state.properties.input = "{{widgets.api_incoming.parsed.client_has_name}}"
        
        # Mettre à jour les transitions
        foreach ($transition in $state.transitions) {
            if ($transition.event -eq "match") {
                $transition.next = "check_client_status_known"
                $transition.conditions[0].friendly_name = "Client Has Name"
                $transition.conditions[0].arguments = @("{{widgets.api_incoming.parsed.client_has_name}}")
            }
        }
    }
    
    # Remplacer check_existing_is_client par check_client_status_known
    if ($state.name -eq "check_existing_is_client") {
        Write-Host "✅ Remplacement de check_existing_is_client par check_client_status_known..." -ForegroundColor Green
        
        $state.name = "check_client_status_known"
        $state.properties.input = "{{widgets.api_incoming.parsed.client_status_known}}"
        
        # Mettre à jour les transitions
        $state.transitions = @(
            @{
                "next" = "ask_is_client_returning"
                "event" = "noMatch"
            },
            @{
                "next" = "menu_principal"
                "event" = "match"
                "conditions" = @(
                    @{
                        "friendly_name" = "Client Status Known"
                        "arguments" = @("{{widgets.api_incoming.parsed.client_status_known}}")
                        "type" = "equal_to"
                        "value" = "true"
                    }
                )
            }
        )
    }
    
    # Mettre à jour delay_welcome pour pointer vers check_client_exists
    if ($state.name -eq "delay_welcome") {
        Write-Host "✅ Mise à jour de delay_welcome..." -ForegroundColor Green
        
        foreach ($transition in $state.transitions) {
            if ($transition.next -eq "check_existing_name") {
                $transition.next = "check_client_exists"
            }
        }
    }
}

Write-Host "💾 Sauvegarde du flow optimisé..." -ForegroundColor Yellow

# Sauvegarder le flow mis à jour
$flow | ConvertTo-Json -Depth 100 | Set-Content $outputPath -Encoding UTF8

Write-Host "✅ Flow optimisé créé avec succès!" -ForegroundColor Green
Write-Host "📁 Fichier de sortie: $outputPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "🎯 Modifications appliquées:" -ForegroundColor Yellow
Write-Host "  ✓ Ajout des champs médias dans api_incoming" -ForegroundColor White
Write-Host "  ✓ check_existing_name → check_client_exists" -ForegroundColor White
Write-Host "  ✓ check_existing_is_client → check_client_status_known" -ForegroundColor White
Write-Host "  ✓ Mise à jour de delay_welcome" -ForegroundColor White
Write-Host ""
Write-Host "📤 Prochaine étape: Importer ce fichier dans Twilio Studio" -ForegroundColor Magenta
