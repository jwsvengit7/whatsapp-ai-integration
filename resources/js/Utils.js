export default class Utils{
    constructor(props) {
    }
     getDateFormat(date){
        const dateObj = new Date(date);

        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        return dateObj.toLocaleDateString('en-US', options);
   }
    getImage(image){
        return `https://uat.smefunds.com/storage/${image}`;
    }
     getRole(role){
        return role.toUpperCase().replaceAll('_',' ')
    }
}


