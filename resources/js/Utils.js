export default class Utils{
    constructor(props) {

    }
    getDateFormat(date){
        const dateObj = new Date(date);

        const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
        return dateObj.toLocaleDateString('en-US', options);
   }
    getRole(role){
        return role.toUpperCase().replaceAll('_',' ')
    }
}


