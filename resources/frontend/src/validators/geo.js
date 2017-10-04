export default function(val) {
  return (val === '') ? true : /^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/.test(val)
}
