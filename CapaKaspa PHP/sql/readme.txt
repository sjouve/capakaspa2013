-- Modification CapaKaspa 2013 --

Table players
- Situation géo n'est plus obligatoire
- Ajout colonne countryCode default FR
- Ajout colonne playerSex char(1) obligatoire default M

Table games
- Modifier type de gameID smallint à int
=> modifier les colonnes gameID qui font référence dans les autres tables : history, messages

Vérifier partie avec message 'undo' au moment de la migration


Ajout table activity
- activityId
- playerId
- typeEvent : history, messages, games
- idEvent
- postDate

Ajout table like_entity
- activityId
- playerId

Ajout table comment
- commentId
- playerId
- typeEntity
- IdEntity
- commentDate
- text

Ajout table privateMessage
- pMessageId
- fromPlayerId
- toPlayerId
- sendDate
- text
- status

Reprise des préférences
=> Ajout préférence language pour tous les anciens user
=> Ajout préférence shareresult
=> Ajout préférence shareinvitation
=> modification préférence theme pour tous les ancien user (passer la valeur à "merida")
=> Supprimer préférence history

Modification table eco
=> Ajout colonne ID et countryLang
=> Ajout des données anglaises
