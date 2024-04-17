# Program pro čtení logovacích zpráv

## Přímý vstup z konzole

Spustíte skript a poté ručně vkládáte logovací řádky přímo do konzole. Skript bude zpracovávat vstup a periodicky vypisovat statistiky. Pro ukončení skriptu můžete použít Ctrl+C.

```bash
php script_infinite.php
```

## Po spuštění můžete začít vkládat logy:

```bash
[2018-03-13 12:16:10] test.DEBUG: Test message [] []
[2018-03-13 12:16:10] test.ERROR: Test message [] []
[2018-03-13 12:16:10] test.WARNING: Test message [] []
[2018-03-13 12:16:10] test.WARNING: Test message [] []
[2018-03-13 12:16:10] test.INFO: Test message [] []
[2018-03-13 12:16:10] test.NOTICE: Test message [] []
[2018-03-13 12:16:10] test.EMERGENCY: Test message [] []
[2018-03-13 12:16:10] test.ALERT: Test message [] []
[2018-03-13 12:16:10] test.ERROR: Test message [] []
[2018-03-13 12:16:10] test.NOTICE: Test message [] []
test.INFO: User logged in
test.ERROR: Database connection failed
```

## Přesměrování z logovacího souboru

Pokud máte logovací soubor, můžete jeho obsah přesměrovat do skriptu, což skriptu umožní zpracovávat logy v reálném čase, jak jsou do souboru zapisovány.

```bash
tail -f /path/to/logfile.log | php script_infinite.php
```

### note:

Tento příkaz využívá tail -f, což je běžný způsob sledování obsahu souboru, který se dynamicky mění. Tento příkaz bude neustále číst nové řádky přidávané na konec souboru a předávat je skriptu.

## Přesměrování z jiného procesu

Můžete také použít výstup z jiné aplikace jako vstup pro skript.

```bash
someCommand | php script_infinite.php
```

## Poznámky k použití

Skript je navržen tak, aby pracoval s logy ve specifickém formátu, který očekává zprávy s prefixem jako test.INFO: nebo test.ERROR:.
K ukončení skriptu, který čte ze stdin, použijte Ctrl+C v případě přímého vstupu, nebo prostě ukončete proces, který generuje data. **Každou operaci potvrďte stisknutím mezerníku!!! I pro ukončení**
