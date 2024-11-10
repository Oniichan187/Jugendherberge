# Jugendherberge

Während der Entwicklung des Projekts habe ich zunächst versucht, Anvil zu verwenden. Leider gab es Probleme mit der Datenbankverbindung – Anvil stellte keine Verbindung zu meiner hochgeladenen Datenbank her, sondern scheinbar zu einer anderen, unbekannten Datenbank. Da ich dieses Problem nicht lösen konnte, entschied ich mich, das Projekt auf herkömmliche Weise mit JavaScript (41.3%), PHP (36.2%), HTML (17.5%) und CSS (5.0%) umzusetzen. Zur lokalen Entwicklung und zum Testen der Datenbankanbindung habe ich XAMPP verwendet.

## Relationenmodell (RM)

### Tabelle `preiskategorien`
| Attribut       | Datentyp | Beschreibung                             |
|----------------|----------|------------------------------------------|
| CategoryID     | INTEGER  | Primärschlüssel, eindeutige ID für die Preiskategorie |
| Name           | TEXT     | Name der Preiskategorie                  |
| Price          | REAL     | Preis pro Nacht                          |

**Beispieldaten:**
| CategoryID | Name     | Price |
|------------|----------|-------|
| 1          | Standard | 50.0  |
| 2          | Deluxe   | 80.0  |
| 3          | Suite    | 120.0 |

---

### Tabelle `gaeste`
| Attribut            | Datentyp | Beschreibung                                  |
|---------------------|----------|-----------------------------------------------|
| GastID              | INTEGER  | Primärschlüssel, eindeutige ID für den Gast   |
| Name                | TEXT     | Name des Gastes                               |
| Email               | TEXT     | E-Mail des Gastes                             |
| PreferredCategoryID | INTEGER  | Fremdschlüssel zu `preiskategorien(CategoryID)` |

**Beispieldaten:**
| GastID | Name            | Email             | PreferredCategoryID |
|--------|------------------|-------------------|----------------------|
| 1      | Max Mustermann  | max@example.com   | 1                    |
| 2      | Anna Beispiel   | anna@example.com  | 2                    |

---

### Tabelle `jugendherbergen`
| Attribut       | Datentyp | Beschreibung                             |
|----------------|----------|------------------------------------------|
| JID            | INTEGER  | Primärschlüssel, eindeutige ID für die Jugendherberge |
| Name           | TEXT     | Name der Jugendherberge                  |

**Beispieldaten:**
| JID | Name                  |
|-----|------------------------|
| 1   | Jugendherberge Berlin  |
| 2   | Jugendherberge München |
| 3   | Jugendherberge Hamburg |

---

### Tabelle `zimmer`
| Attribut     | Datentyp | Beschreibung                             |
|--------------|----------|------------------------------------------|
| ZimmerID     | INTEGER  | Primärschlüssel, eindeutige ID für das Zimmer |
| JID          | INTEGER  | Fremdschlüssel zu `jugendherbergen(JID)` |
| RoomNumber   | INTEGER  | Zimmernummer in der Jugendherberge       |
| BedCount     | INTEGER  | Anzahl der Betten im Zimmer              |
| CategoryID   | INTEGER  | Fremdschlüssel zu `preiskategorien(CategoryID)` |
| Availability | TEXT     | Verfügbarkeitsstatus des Zimmers         |

**Beispieldaten:**
| ZimmerID | JID | RoomNumber | BedCount | CategoryID | Availability |
|----------|-----|------------|----------|------------|--------------|
| 1        | 1   | 101        | 2        | 1          | Available    |
| 2        | 1   | 102        | 4        | 2          | Available    |
| 3        | 2   | 201        | 2        | 1          | Available    |
| 4        | 3   | 301        | 3        | 3          | Available    |

---

### Tabelle `buchungen`
| Attribut      | Datentyp | Beschreibung                              |
|---------------|----------|-------------------------------------------|
| BuchungID     | INTEGER  | Primärschlüssel, eindeutige ID für die Buchung |
| ZimmerID      | INTEGER  | Fremdschlüssel zu `zimmer(ZimmerID)`       |
| CheckInDatum  | TEXT     | Datum des Check-ins                       |
| CheckOutDatum | TEXT     | Datum des Check-outs                      |

**Beispieldaten:**
| BuchungID | ZimmerID | CheckInDatum | CheckOutDatum |
|-----------|----------|--------------|---------------|
| 1         | 1        | 2024-11-10   | 2024-11-15    |
| 2         | 2        | 2024-11-26   | 2024-11-30    |

---

### Tabelle `buchung_gaeste`
| Attribut  | Datentyp | Beschreibung                              |
|-----------|----------|-------------------------------------------|
| BuchungID | INTEGER  | Fremdschlüssel zu `buchungen(BuchungID)`  |
| GastID    | INTEGER  | Fremdschlüssel zu `gaeste(GastID)`        |

**Beispieldaten:**
| BuchungID | GastID |
|-----------|--------|
| 1         | 1      |
| 2         | 1      |
| 2         | 2      |

---

### Beziehungen
1. **1:N Beziehung** zwischen `preiskategorien` und `zimmer` (eine Preiskategorie kann mehreren Zimmern zugeordnet sein).
2. **1:N Beziehung** zwischen `jugendherbergen` und `zimmer` (eine Jugendherberge kann mehrere Zimmer haben).
3. **1:N Beziehung** zwischen `zimmer` und `buchungen` (jedes Zimmer kann in mehreren Buchungen vorkommen).
4. **N:M Beziehung** zwischen `buchungen` und `gaeste` durch die Verbindungstabelle `buchung_gaeste` (eine Buchung kann mehrere Gäste umfassen, und ein Gast kann an mehreren Buchungen beteiligt sein).
