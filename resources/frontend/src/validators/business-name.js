export default function(val) {
  return (val === '') ? true : /^[0-9A-Za-z'-]+(?:\s[0-9A-Za-z'-,\.]+)*$/.test(val)
}
