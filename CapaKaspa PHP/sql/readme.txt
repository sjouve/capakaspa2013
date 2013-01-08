-- Modification CapaKaspa 2013 --

Table players
- Situation géo n'est plus obligatoire
- Ajout colonne countryCode

Table games
- Modifier type de gameID smallint à int
=> modifier les colonnes gameID qui font référence dans les autres tables : history, messages

NOTE : Problème avec la clef composée pour history

Ajout table activity
- activityId
- playerId
- typeEvent : history, messages, games
- idEvent
- postDate

Ajout table activityNotation
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
