import sys, os
import falcon
import json
import app.main
  
class routes():
  def __init__(self, api):
    file_name = os.path.splitext(os.path.basename(__file__))[0]
    api.add_route(f'/{file_name}', self)
  def on_post(self, req, resp):
    data = json.load(req.bounded_stream)
    # print(data)
    query = data.get('query')
    user_id = req.get_param('user_id')
    save_path = f'var/import/{user_id}'
    result, comments = app.main.query(query, save_path)
    response = {
      "result": result,
      "comments": comments
    }
    resp.text = json.dumps(response)
    resp.status = falcon.HTTP_201