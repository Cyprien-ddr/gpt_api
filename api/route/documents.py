import sys, os
import falcon
import json
import app.documents
  
class routes():
  def __init__(self, api):
    file_name = os.path.splitext(os.path.basename(__file__))[0]
    api.add_route(f'/{file_name}', self)
  def on_get(self, req, resp):
    #https://github.com/yohanboniface/falcon-multipart
    user_id = req.get_param('user_id')
    save_path = f'var/import/{user_id}'
    if not os.path.exists(save_path):
      os.makedirs(save_path)
    comments = ""
    response = {
      "documents": app.documents.list(save_path),
      "comments": comments
    }
    resp.text = json.dumps(response)
    resp.status = falcon.HTTP_201