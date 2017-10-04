export default function(val) {
  return (val === '') ? true : /^[0-9a-zA-Z]{4,9}$/.test(val)
}
