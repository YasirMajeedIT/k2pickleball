$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$current = Get-Content $hostsPath -Raw

$entriesToAdd = @(
    "127.0.0.1       demo-sports-club.k2pickleball.local"
)

$toAppend = @()
foreach ($entry in $entriesToAdd) {
    $hostname = ($entry -split '\s+')[1]
    if ($current -notmatch [regex]::Escape($hostname)) {
        $toAppend += $entry
    }
}

if ($toAppend.Count -gt 0) {
    $block = "`r`n# K2 Pickleball tenant organizations`r`n" + ($toAppend -join "`r`n")
    Add-Content -Path $hostsPath -Value $block -Encoding UTF8
    Write-Host "Added $($toAppend.Count) entries to hosts file."
    foreach ($e in $toAppend) { Write-Host "  + $e" }
} else {
    Write-Host "All entries already present."
}
