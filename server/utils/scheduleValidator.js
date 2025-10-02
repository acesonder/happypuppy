const isValidSchedule = (date, time) => {
  const selectedDate = new Date(date);
  const dayOfWeek = selectedDate.getDay();
  
  // Only Wednesday (3) and Friday (5) are allowed
  if (dayOfWeek !== 3 && dayOfWeek !== 5) {
    return { valid: false, message: 'Pickup/delivery only available on Wednesdays and Fridays' };
  }
  
  // Parse time (format: "17:00" or "5:00 PM")
  const timeMatch = time.match(/(\d+):(\d+)/);
  if (!timeMatch) {
    return { valid: false, message: 'Invalid time format' };
  }
  
  const hours = parseInt(timeMatch[1]);
  const minutes = parseInt(timeMatch[2]);
  
  // Check if time is between 5pm (17:00) and 9pm (21:00)
  if (hours < 17 || hours >= 21) {
    return { valid: false, message: 'Pickup/delivery only available between 5pm and 9pm' };
  }
  
  return { valid: true, message: 'Valid schedule' };
};

module.exports = { isValidSchedule };
