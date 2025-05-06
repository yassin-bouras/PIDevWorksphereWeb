# Worksphere 🌐

Worksphere est une application complète de **gestion des ressources humaines** développée avec Symfony et MySQL. Conçue pour simplifier les tâches administratives RH, elle offre une suite d'outils pour gérer efficacement les employés, organiser des événements, coordonner des formations et superviser des projets. Ce projet a été réalisé dans le cadre du cours PIDEV 3A à **l'École supérieure privée d'ingénierie et de technologie**, mettant l'accent sur le développement web avancé et l'intelligence artificielle.

---

## ✨ Fonctionnalités  

### Fonctionnalités principales  
- **🔐 Authentification sécurisée** : Connexion, inscription et gestion de profil  
- **👥 Gestion des employés** : Opérations CRUD complètes  
- **📅 Gestion des événements** : Organisation et suivi des événements d'entreprise  
- **🎓 Gestion des formations** : Création et gestion des sessions de formation  
- **📊 Gestion des projets** : Création de projets, affectation d'équipes et suivi de progression des taches 
- **🤝 Gestion des équipes** : Création des équipes 
- **📝 Système de réservation** : Réservation de places pour les formations  
- **📢 Système de réclamation** : Soumission et résolution des plaintes  
- **💼 Gestion des offres d'emploi** : Publication et gestion des opportunités  
- **🗣️ Gestion des entretiens** : Planification et conduite des entretiens  
- **💬 Système de feedback** : Collecte et analyse des retours d'entretiens  

### Fonctionnalités avancées  
- **👁️ Reconnaissance faciale** : Authentification sécurisée par IA  
- **🔑 Connexion via Google (OAuth)** : Simplification de la connexion  
- **🚫 Bannissement des utilisateurs** : Outils de modération avancés  
- **🤖 Suggestions de sponsors par IA** : Recommandations intelligentes pour événements  
- **☁️ Stockage cloud** : Sauvegarde sécurisée des données  
- **💻 Réunions virtuelles** : Sessions de formation en ligne intégrées  
- **📄 Filtrage intelligent des CV** : Analyse automatisée des candidatures 

---

## 🛠️ Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/yourusername/worksphere.git
   cd worksphere
2. **Configurer l'environnement**
   Modifier le fichier .env :
   ```bash
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
  
3. **Configurer la base de données**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load

## 💻 Technologies utilisées

### Backend
![Symfony](https://img.shields.io/badge/Symfony-6.4-000000?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)

### Base de données
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

### Frontend
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

### Intelligence Artificielle
![Python](https://img.shields.io/badge/Python-3.10-3776AB?style=for-the-badge&logo=python&logoColor=white)
![TensorFlow](https://img.shields.io/badge/TensorFlow-2.10-FF6F00?style=for-the-badge&logo=tensorflow&logoColor=white)
![OpenCV](https://img.shields.io/badge/OpenCV-4.7-5C3EE8?style=for-the-badge&logo=opencv&logoColor=white)

### Outils
![Git](https://img.shields.io/badge/Git-F05032?style=for-the-badge&logo=git&logoColor=white)


## 📬 Contact
Équipe de développement :

Yassine Bouras - yassine.bouras@esprit.tn

Eya Kassous - eya.kassous@esprit.tn

Houssem Hbeib - houssem.hbeib@esprit.tn

Molka Gharbi - molka.gharbi@esprit.tn

Jacem Jouili - jacem.jouili@esprit.tn

Asma Sellami - asma.sellami@esprit.tn

## 📜 Licence
Ce projet est sous licence MIT – voir le fichier LICENSE pour plus de détails.

## 🙏 Remerciements
Ce projet a été développé dans le cadre du cours PIDEV 3A **l'École supérieure privée d'ingénierie et de technologie**. Nous tenons à remercier nos professeurs et mentors pour leur précieux encadrement et leur soutien tout au long du développement.

