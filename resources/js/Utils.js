export default class Utils{
    constructor(props) {
    }
     getDateFormat(date){
        const dateObj = new Date(date);

        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        return dateObj.toLocaleDateString('en-US', options);
   }
    getImage(image){
        return `http://localhost:8000/storage/${image}`;
    }
     getRole(role){
        return role.toUpperCase().replaceAll('_',' ')
    }
}


